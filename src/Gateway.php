<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: 2005-2022 Pronamic
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
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = array(
			'payment_status_request',
		);

		// Client.
		$this->client = new Client( $config->token, $config->service_id );
	}

	/**
	 * Get issuers
	 *
	 * @see Core_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$result = $this->client->get_issuers();

		if ( is_array( $result ) ) {
			$groups[] = array(
				'options' => $result,
			);
		}

		return $groups;
	}

	/**
	 * Get supported payment methods
	 *
	 * @see Core_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::AFTERPAY_NL,
			PaymentMethods::BANCONTACT,
			PaymentMethods::BANK_TRANSFER,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::FOCUM,
			PaymentMethods::GIROPAY,
			PaymentMethods::IDEAL,
			PaymentMethods::IN3,
			PaymentMethods::KLARNA_PAY_LATER,
			PaymentMethods::MAESTRO,
			PaymentMethods::PAYPAL,
			PaymentMethods::SOFORT,
			PaymentMethods::SPRAYPAY,
		);
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
		$end_user = array();

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
			$address = array(
				'streetName'            => $shipping_address->get_street_name(),
				'streetNumber'          => $shipping_address->get_house_number_base(),
				'streetNumberExtension' => $shipping_address->get_house_number_addition(),
				'zipCode'               => $shipping_address->get_postal_code(),
				'city'                  => $shipping_address->get_city(),
				'countryCode'           => $shipping_address->get_country_code(),
			);

			$end_user['address'] = $address;
		}

		/**
		 * End user - Invoice address.
		 */
		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$address = array(
				'streetName'            => $billing_address->get_street_name(),
				'streetNumber'          => $billing_address->get_house_number_base(),
				'streetNumberExtension' => $billing_address->get_house_number_addition(),
				'zipCode'               => $billing_address->get_postal_code(),
				'city'                  => $billing_address->get_city(),
				'countryCode'           => $billing_address->get_country_code(),
			);

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
		$sale_data = array(
			'invoiceDate'  => $payment->get_date()->format( 'd-m-Y' ),
			'deliveryDate' => $payment->get_date()->format( 'd-m-Y' ),
		);

		$payment_lines = $payment->get_lines();

		if ( null !== $payment_lines ) {
			$sale_data['order_data'] = array();

			foreach ( $payment_lines as $line ) {
				$order_data_item = array(
					'productId'   => $line->get_id(),
					'productType' => ProductTypes::transform( $line->get_type() ),
					'description' => $line->get_name(),
					'quantity'    => $line->get_quantity(),
				);

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
		$request = array(
			'transaction' => array(
				'currency'    => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
				'description' => $payment->get_description(),
			),
			'enduser'     => $end_user,
			'saleData'    => $sale_data,
		);

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
