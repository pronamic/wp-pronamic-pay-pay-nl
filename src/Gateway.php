<?php

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.7
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Gateway extends Pronamic_WP_Pay_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'pay_nl';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Pay.nl gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_PayNL_Config $config
	 */
	public function __construct( Pronamic_WP_Pay_Gateways_PayNL_Config $config ) {
		parent::__construct( $config );

		$this->supports = array(
			'payment_status_request',
		);

		$this->set_method( Pronamic_WP_Pay_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 1.20 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_WP_Pay_Gateways_PayNL_Client( $config->token, $config->service_id );
	}

	/////////////////////////////////////////////////

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

	public function get_issuer_field() {
		if ( Pronamic_WP_Pay_PaymentMethods::IDEAL === $this->get_payment_method() ) {
			return array(
				'id'       => 'pronamic_ideal_issuer_id',
				'name'     => 'pronamic_ideal_issuer_id',
				'label'    => __( 'Choose your bank', 'pronamic_ideal' ),
				'required' => true,
				'type'     => 'select',
				'choices'  => $this->get_transient_issuers(),
			);
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			Pronamic_WP_Pay_PaymentMethods::IDEAL,
			Pronamic_WP_Pay_PaymentMethods::BANCONTACT,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Pronamic_Pay_Payment $payment ) {
		$request = array(
			'enduser' => array(
				'lastName'     => $payment->get_customer_name(),
				'emailAddress' => $payment->get_email(),
			),
		);

		switch ( $payment->get_method() ) {
			case Pronamic_WP_Pay_PaymentMethods::BANCONTACT :
			case Pronamic_WP_Pay_PaymentMethods::MISTER_CASH :
				$request['paymentOptionId'] = Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods::MISTERCASH;

				break;
			case Pronamic_WP_Pay_PaymentMethods::IDEAL :
				$request['paymentOptionId']    = Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods::IDEAL;
				$request['paymentOptionSubId'] = $payment->get_issuer();

				break;
		}

		$result = $this->client->transaction_start(
			$payment->get_amount(),
			Pronamic_WP_Pay_Gateways_PayNL_Util::get_ip_address(),
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

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$result = $this->client->transaction_info( $payment->get_transaction_id() );

		if ( isset( $result, $result->paymentDetails ) ) {
			$state = $result->paymentDetails->state;

			$status = Pronamic_WP_Pay_Gateways_PayNL_States::transform( $state );

			$payment->set_status( $status );
		}
	}
}
