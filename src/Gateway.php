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
	 * Constructs and initializes an Pay.nl gateway
	 *
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->supports = array(
			'payment_status_request',
		);

		$this->set_method( Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 1.20 );
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
			PaymentMethods::BANCONTACT,
			PaymentMethods::BANK_TRANSFER,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::GIROPAY,
			PaymentMethods::IDEAL,
			PaymentMethods::PAYPAL,
			PaymentMethods::SOFORT,
		);
	}

	/**
	 * Start
	 *
	 * @see Core_Gateway::start()
	 *
	 * @param Payment $payment
	 */
	public function start( Payment $payment ) {
		$payment_method = $payment->get_method();

		$request = array(
			'enduser' => array(
				'lastName'     => $payment->get_customer_name(),
				'emailAddress' => $payment->get_email(),
			),
		);

		switch ( $payment_method ) {
			case PaymentMethods::IDEAL:
				$request['paymentOptionId']    = Methods::IDEAL;
				$request['paymentOptionSubId'] = $payment->get_issuer();

				break;
			default:
				$method = Methods::transform( $payment_method );

				if ( $method ) {
					$request['paymentOptionId'] = $method;
				}

				if ( ! isset( $request['paymentOptionId'] ) && ! empty( $payment_method ) ) {
					// Leap of faith if the WordPress payment method could not transform to a Mollie method?
					$request['paymentOptionId'] = $payment_method;
				}
		}

		// Set transaction description.
		// @see https://admin.pay.nl/docpanel/api/Transaction/start/4
		$request['transaction'] = array(
			'description' => $payment->get_description(),
		);

		$result = $this->client->transaction_start(
			$payment->get_amount()->get_amount(),
			Util::get_ip_address(),
			rawurlencode( $payment->get_return_url() ),
			$request
		);

		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return;
		}

		$payment->set_transaction_id( $result->transaction->transactionId );
		$payment->set_action_url( $result->transaction->paymentURL );
	}

	/**
	 * Update status of the specified payment
	 *
	 * @param Payment $payment
	 */
	public function update_status( Payment $payment ) {
		$result = $this->client->transaction_info( $payment->get_transaction_id() );

		if ( isset( $result, $result->paymentDetails ) ) {
			$state = $result->paymentDetails->state;

			$status = Statuses::transform( $state );

			$payment->set_status( $status );
		}
	}
}
