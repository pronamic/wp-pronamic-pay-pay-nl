<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Fields\CachedCallbackOptions;
use Pronamic\WordPress\Pay\Fields\IDealIssuerSelectField;
use Pronamic\WordPress\Pay\Fields\SelectFieldOption;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: 2005-2024 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 3.0.1
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructs and initializes an Pay.nl gateway
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = [
			'payment_status_request',
		];

		// Client.
		$this->client = new Client( $config->token, $config->service_id );

		// Methods.
		$ideal_payment_method = new PaymentMethod( PaymentMethods::IDEAL );

		$ideal_issuer_field = new IDealIssuerSelectField( 'ideal-issuer' );

		$ideal_issuer_field->set_required( true );

		$ideal_issuer_field->set_options(
			new CachedCallbackOptions(
				function() {
					return $this->get_ideal_issuers();
				},
				'pronamic_pay_ideal_issuers_' . \md5( \wp_json_encode( $config ) )
			)
		);

		$ideal_payment_method->add_field( $ideal_issuer_field );

		$this->register_payment_method( new PaymentMethod( PaymentMethods::AFTERPAY_NL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANCONTACT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANK_TRANSFER ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::CREDIT_CARD ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::FOCUM ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::GIROPAY ) );
		$this->register_payment_method( $ideal_payment_method );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::IN3 ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::KLARNA_PAY_LATER ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MAESTRO ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::PAYPAL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::RIVERTY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SOFORT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SPRAYPAY ) );
	}

	/**
	 * Get iDEAL issuers.
	 *
	 * @return array<SelectFieldOption>
	 */
	private function get_ideal_issuers() {
		$result = $this->client->get_issuers();

		$options = [];

		foreach ( $result as $key => $value ) {
			$options[] = new SelectFieldOption( $key, $value );
		}

		return $options;
	}

	/**
	 * Start.
	 *
	 * @see Core_Gateway::start()
	 *
	 * @param Payment $payment Payment.
	 */
	public function start( Payment $payment ) {
		$payment_method = $payment->get_payment_method();

		$customer = $payment->get_customer();

		/**
		 * End user.
		 */
		$end_user = [];

		if ( null !== $customer ) {
			$end_user['gender']       = $customer->get_gender();
			$end_user['phoneNumber']  = $customer->get_phone();
			$end_user['emailAddress'] = $customer->get_email();
			$end_user['language']     = $customer->get_language();

			/**
			 * Name.
			 */
			$name = $customer->get_name();

			if ( null !== $name ) {
				$end_user['initials'] = \substr( (string) $name->get_first_name(), 0, 32 );
				$end_user['lastName'] = \substr( (string) $name->get_last_name(), 0, 32 );
			}

			/**
			 * Date of Birth.
			 */
			$birth_date = $customer->get_birth_date();

			if ( $birth_date instanceof \DateTimeInterface ) {
				$end_user['dob'] = $birth_date->format( 'dmY' );
			}
		}

		/**
		 * End user - Address.
		 */
		$shipping_address = $payment->get_shipping_address();

		if ( null !== $shipping_address ) {
			$address = [
				'streetName'            => $shipping_address->get_street_name(),
				'streetNumber'          => $shipping_address->get_house_number_base(),
				'streetNumberExtension' => $shipping_address->get_house_number_addition(),
				'zipCode'               => $shipping_address->get_postal_code(),
				'city'                  => $shipping_address->get_city(),
				'countryCode'           => $shipping_address->get_country_code(),
			];

			$end_user['address'] = $address;
		}

		/**
		 * End user - Invoice address.
		 */
		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$address = [
				'streetName'            => $billing_address->get_street_name(),
				'streetNumber'          => $billing_address->get_house_number_base(),
				'streetNumberExtension' => $billing_address->get_house_number_addition(),
				'zipCode'               => $billing_address->get_postal_code(),
				'city'                  => $billing_address->get_city(),
				'countryCode'           => $billing_address->get_country_code(),
			];

			if ( \array_key_exists( 'gender', $end_user ) ) {
				$address['gender'] = $end_user['gender'];
			}

			if ( \array_key_exists( 'initials', $end_user ) ) {
				$address['initials'] = $end_user['initials'];
			}

			if ( \array_key_exists( 'lastName', $end_user ) ) {
				$address['lastName'] = $end_user['lastName'];
			}

			$end_user['invoiceAddress'] = $address;
		}

		/**
		 * Sale data.
		 */
		$sale_data = [
			'invoiceDate'  => $payment->get_date()->format( 'd-m-Y' ),
			'deliveryDate' => $payment->get_date()->format( 'd-m-Y' ),
		];

		$payment_lines = $payment->get_lines();

		if ( null !== $payment_lines ) {
			$sale_data['order_data'] = [];

			foreach ( $payment_lines as $line ) {
				$order_data_item = [
					'productId'   => $line->get_id(),
					'productType' => ProductTypes::transform( $line->get_type() ),
					'description' => $line->get_name(),
					'quantity'    => $line->get_quantity(),
				];

				$unit_price = $line->get_unit_price();

				if ( null !== $unit_price ) {
					$order_data_item['price'] = $unit_price->get_minor_units()->to_int();
				}

				$sale_data['order_data'][] = $order_data_item;
			}
		}

		/**
		 * Request.
		 *
		 * @link https://docs.pay.nl/developers?language=nl#transaction-process
		 */
		$request = [
			'transaction' => [
				'currency'    => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
				'description' => $payment->get_description(),
			],
			'statsData'   => [
				/**
				 * Info.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The used info code which can be tracked in the stats."
				 *
				 * From https://docs.pay.nl/developers#transaction-paylater:
				 * > "Variabele 'info' die kan worden getraceerd in de statistieken"
				 */
				'info'   => 'Pronamic Pay payment ' . $payment->get_id(),
				/**
				 * Tool.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The used tool code which can be tracked in the stats."
				 *
				 * From https://docs.pay.nl/developers#transaction-paylater:
				 * > "Variabele 'tool' die kan worden getraceerd in de statistieken"
				 */
				'tool'   => 'Pronamic Pay ' . \pronamic_pay_plugin()->get_version(),
				/**
				 * Extra 1.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The first free value which can be tracked in the stats."
				 *
				 * From https://docs.pay.nl/developers#transaction-paylater:
				 * > "Vrije variabele 'extra1' die kan worden getraceerd in de statistieken (advies: ID van de order)."
				 */
				'extra1' => $payment->get_id(),
				/**
				 * Extra 2.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The second free value which can be tracked in the stats."
				 *
				 * From https://docs.pay.nl/developers#transaction-paylater:
				 * > "Vrije variabele 'extra2' die kan worden getraceerd in de statistieken (advies: klant referentie)."
				 */
				'extra2' => \get_current_user_id(),
				/**
				 * Extra 3.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The third free value which can be tracked in the stats."
				 *
				 * From https://docs.pay.nl/developers#transaction-paylater:
				 * > "Vrije variabele 'extra3' die kan worden getraceerd in de statistieken"
				 */
				'extra3' => $payment->get_source() . ' - ' . $payment->get_source_id(),
				/**
				 * Object.
				 *
				 * From https://developer.pay.nl/reference/post_transactions:
				 * > "The object which can be tracked in stats."
				 *
				 * From https://docs.pay.nl/developers#mandatory-data-technical-partners:
				 * > "Naam van het platform of de technische partner, eventueel gevolgd door een pipeline met versienummers"
				 */
				'object' => implode(
					' | ',
					[
						/**
						 * Pronamic Pay version.
						 *
						 * @link https://github.com/pronamic/pronamic-pay/issues/12
						 */
						'PronamicPay/' . \pronamic_pay_plugin()->get_version(),
						/**
						 * WordPress version.
						 *
						 * @link https://github.com/WordPress/WordPress/blob/f9db66d504fc72942515f6c0ed2b63aee7cef876/wp-includes/class-wp-http.php#L183-L192
						 */
						'WordPress/' . get_bloginfo( 'version' ),
					]
				),
			],
			'enduser'     => $end_user,
			'saleData'    => $sale_data,
		];

		// Payment method.
		$method = Methods::transform( $payment_method );

		if ( null !== $method ) {
			$request['paymentOptionId'] = $method;
		}

		// Set payment method specific parameters.
		if ( PaymentMethods::IDEAL === $payment_method ) {
			$request['paymentOptionSubId'] = $payment->get_meta( 'issuer' );
		}

		// Start transaction.
		$result = $this->client->transaction_start(
			$payment->get_total_amount()->get_minor_units()->to_int(),
			Util::get_ip_address(),
			$payment->get_return_url(),
			$request
		);

		// Update gateway results in payment.
		$payment->set_transaction_id( $result->transaction->transactionId );
		$payment->set_action_url( $result->transaction->paymentURL );
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 */
	public function update_status( Payment $payment ) {
		try {
			// Get transaction info.
			$result = $this->client->transaction_info( $payment->get_transaction_id() );
		} catch ( \Exception $e ) {
			return;
		}

		if ( is_object( $result ) && isset( $result->paymentDetails ) ) {
			$status = Statuses::transform( $result->paymentDetails->state );

			// Update payment status.
			$payment->set_status( $status );
		}
	}
}
