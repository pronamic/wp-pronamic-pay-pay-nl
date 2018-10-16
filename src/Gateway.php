<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'pay_nl';

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

		$this->supports = array(
			'payment_status_request',
		);

		$this->set_method( self::METHOD_HTTP_REDIRECT );
		$this->set_slug( self::SLUG );

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

		if ( $result ) {
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
		$customer        = $payment->get_customer();
		$birth_date      = $payment->get_customer()->get_birth_date();
		$billing_address = $payment->get_billing_address();

		// Payment lines.
		$order_data = array();

		foreach ( $payment->get_lines() as $line ) {
			$order_data[] = array(
				'productId'   => $line->get_id(),
				'productType' => $line->get_type(), // ARTICLE, SHIPPING, HANDLING, DISCOUNT.
				'description' => $line->get_description(),
				'price'       => $line->get_unit_price(),
				'quantity'    => $line->get_quantity(),
			);
		}

		$request = array(
			// Transaction.
			'transaction'     => array(
				'currency'    => $payment->get_currency(),
				'description' => $payment->get_description(),
			),

			// Payment method.
			'paymentOptionId' => Methods::transform( $payment_method ),

			// End user.
			'enduser'         => array(
				'initials'       => $customer->get_name()->get_first_name(),
				'lastName'       => $customer->get_name()->get_last_name(),
				'gender'         => $customer->get_gender(),
				'dob'            => ( $birth_date instanceof \DateTime ) ? $birth_date->format( 'dmY' ) : null,
				'phoneNumber'    => $customer->get_phone(),
				'emailAddress'   => $customer->get_email(),
				'language'       => $customer->get_language(),

				// Address.
				'address'        => array(
					'streetName'            => $billing_address->get_street_name(),
					'streetNumber'          => $billing_address->get_house_number(),
					'streetNumberExtension' => $billing_address->get_house_number_addition(),
					'zipCode'               => $billing_address->get_postal_code(),
					'city'                  => $billing_address->get_city(),
					'countryCode'           => $billing_address->get_country_code(),
				),

				// Invoice address.
				'invoiceAddress' => array(
					'initials'              => $customer->get_name()->get_first_name(),
					'lastName'              => $customer->get_name()->get_last_name(),
					'gender'                => $customer->get_gender(),
					'streetName'            => $billing_address->get_street_name(),
					'streetNumber'          => $billing_address->get_house_number(),
					'streetNumberExtension' => $billing_address->get_house_number_addition(),
					'zipCode'               => $billing_address->get_postal_code(),
					'city'                  => $billing_address->get_city(),
					'countryCode'           => $billing_address->get_country_code(),
				),
			),

			// Sale data.
			'saleData'        => array(
				'invoiceDate'  => $payment->get_date()->format( 'd-m-Y' ),
				'deliveryDate' => $payment->get_date()->format( 'd-m-Y' ),
				'orderData'    => $order_data,
			),
		);

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
			$payment->get_amount()->get_amount(),
			Util::get_ip_address(),
			rawurlencode( $payment->get_return_url() ),
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

		if ( isset( $result->paymentDetails ) ) {
			$status = Statuses::transform( $result->paymentDetails->state );

			// Update payment status.
			$payment->set_status( $status );
		}
	}
}
