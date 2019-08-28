<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.1
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
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$result = $this->client->get_issuers();

		$this->error = $this->client->get_error();

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
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::AFTERPAY,
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
		$payment_method = $payment->get_method();

		/*
		 * New transaction request.
		 * @link https://www.pay.nl/docs/developers.php#transactions
		 */
		$customer         = $payment->get_customer();
		$billing_address  = $payment->get_billing_address();
		$shipping_address = $payment->get_shipping_address();

		// Payment lines.
		$order_data = array();

		if ( null !== $payment->get_lines() ) {
			foreach ( $payment->get_lines() as $line ) {
				$price = null;

				if ( null !== $line->get_unit_price() ) {
					$price = $line->get_unit_price()->get_including_tax()->get_cents();
				}

				$order_data[] = array(
					'productId'   => $line->get_id(),
					'productType' => ProductTypes::transform( $line->get_type() ),
					'description' => $line->get_name(),
					'price'       => $price,
					'quantity'    => $line->get_quantity(),
				);
			}
		}

		// End user.
		$end_user = array();

		// End user - Address.
		if ( null !== $shipping_address ) {
			$end_user['address'] = array(
				'streetName'            => $shipping_address->get_street_name(),
				'streetNumber'          => $shipping_address->get_house_number_base(),
				'streetNumberExtension' => $shipping_address->get_house_number_addition(),
				'zipCode'               => $shipping_address->get_postal_code(),
				'city'                  => $shipping_address->get_city(),
				'countryCode'           => $shipping_address->get_country_code(),
			);
		}

		// End user - Invoice address.
		if ( null !== $billing_address ) {
			$end_user['invoiceAddress'] = array(
				'streetName'            => $billing_address->get_street_name(),
				'streetNumber'          => $billing_address->get_house_number_base(),
				'streetNumberExtension' => $billing_address->get_house_number_addition(),
				'zipCode'               => $billing_address->get_postal_code(),
				'city'                  => $billing_address->get_city(),
				'countryCode'           => $billing_address->get_country_code(),
			);
		}

		// Request.
		$request = array(
			// Transaction.
			'transaction' => array(
				'currency'    => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
				'description' => $payment->get_description(),
			),

			// End user.
			'enduser'     => $end_user,

			// Sale data.
			'saleData'    => array(
				'invoiceDate'  => $payment->get_date()->format( 'd-m-Y' ),
				'deliveryDate' => $payment->get_date()->format( 'd-m-Y' ),
				'orderData'    => $order_data,
			),
		);

		// Payment method.
		$method = Methods::transform( $payment_method );

		if ( null !== $method ) {
			$request['paymentOptionId'] = $method;
		}

		if ( null !== $payment->get_customer() ) {
			$enduser = array(
				'gender'       => $customer->get_gender(),
				'phoneNumber'  => $customer->get_phone(),
				'emailAddress' => $customer->get_email(),
				'language'     => $customer->get_language(),
			);

			$invoice_address = array(
				'gender' => $customer->get_gender(),
			);

			// Set name from customer.
			if ( null !== $customer->get_name() ) {
				$enduser['initials'] = $customer->get_name()->get_first_name();
				$enduser['lastName'] = $customer->get_name()->get_last_name();

				$invoice_address['initials'] = $customer->get_name()->get_first_name();
				$invoice_address['lastName'] = $customer->get_name()->get_last_name();
			}

			// Set date of birth.
			if ( $customer->get_birth_date() instanceof \DateTime ) {
				$enduser['dob'] = $customer->get_birth_date()->format( 'dmY' );
			}

			$request['enduser'] = array_merge( $request['enduser'], $enduser );

			$request['enduser']['invoiceAddress'] = array_merge( $request['enduser']['invoiceAddress'], $invoice_address );
		}

		// Check payment method.
		if ( null === $request['paymentOptionId'] && ! empty( $payment_method ) ) {
			// Leap of faith if the WordPress payment method could not transform to a Pay.nl method?
			$request['paymentOptionId'] = $payment_method;
		}

		// Set payment method specific parameters.
		if ( PaymentMethods::IDEAL === $payment_method ) {
			$request['paymentOptionSubId'] = $payment->get_issuer();
		}

		// Start transaction.
		$result = $this->client->transaction_start(
			$payment->get_total_amount()->get_cents(),
			Util::get_ip_address(),
			$payment->get_return_url(),
			$request
		);

		// Handle errors.
		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return;
		}

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
		// Get transaction info.
		$result = $this->client->transaction_info( $payment->get_transaction_id() );

		if ( is_object( $result ) && isset( $result->paymentDetails ) ) {
			$status = Statuses::transform( $result->paymentDetails->state );

			// Update payment status.
			$payment->set_status( $status );
		}
	}
}
