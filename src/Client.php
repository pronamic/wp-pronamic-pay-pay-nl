<?php

/**
 * Title: Pay.nl client
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Client {
	/**
	 * API URL
	 *
	 * @var string
	 */
	const API_URL = 'https://rest-api.pay.nl/%s/%s/%s/%s/';

	/////////////////////////////////////////////////

	/**
	 * Error
	 *
	 * @var WP_Error
	 */
	private $error;

	/////////////////////////////////////////////////

	/**
	 * Construct and initialize an Pay.nl client
	 *
	 * @param string $token
	 * @param string $service_id
	 */
	public function __construct( $token, $service_id ) {
		$this->token      = $token;
		$this->service_id = $service_id;
	}

	/////////////////////////////////////////////////

	/**
	 * Get latest error
	 *
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/////////////////////////////////////////////////

	/**
	 * Get Pay.nl API URL
	 *
	 * @param string $version
	 * @param string $namespace
	 * @param string $method
	 * @param string $output
	 * @param array $parameters
	 */
	private function get_url( $version, $namespace, $method, $output, $parameters = array() ) {
		return add_query_arg( $parameters, sprintf(
			self::API_URL,
			$version,
			$namespace,
			$method,
			$output
		) );
	}

	/////////////////////////////////////////////////

	/**
	 * Parse reponse
	 *
	 * @param stdClass $data
	 * @return Ambigous <NULL, stdClass>
	 */
	private function parse_response( $data ) {
		$result = null;

		if ( isset( $data, $data->request, $data->request->result ) ) {
			if ( 0 == $data->request->result && isset( $data->request->errorId, $data->request->errorMessage ) ) { // WPCS: loose comparison ok.
				$pay_nl_error = new Pronamic_WP_Pay_Gateways_PayNL_Error( $data->request->errorId, $data->request->errorMessage );

				$this->error = new WP_Error( 'pay_nl_error', (string) $pay_nl_error, $pay_nl_error );
			}

			$result = $data;
		}

		return $result;
	}

	/////////////////////////////////////////////////

	/**
	 * Transaction start
	 *
	 * @param float $amount
	 * @param string $ip_address
	 * @param string $finish_url
	 * @return stdClass
	 *
	 * @see https://admin.pay.nl/docpanel/api/Transaction/start/4
	 */
	public function transaction_start( $amount, $ip_address, $finish_url, $request_param = array() ) {
		$result = null;

		$parameters = array_merge(
			$request_param,
			array(
				'token'     => $this->token,
				'serviceId' => $this->service_id,
				'amount'    => Pronamic_WP_Util::amount_to_cents( $amount ),
				'ipAddress' => $ip_address,
				'finishUrl' => $finish_url,
			)
		);

		// URL
		$url = $this->get_url( 'v4', 'Transaction', 'start', 'json', $parameters );

		// Request
		$response = wp_remote_get( $url );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			$body = wp_remote_retrieve_body( $response );

			$data = json_decode( $body );

			$result = $this->parse_response( $data );
		}

		// Return result
		return $result;
	}

	/**
	 * Transaction info
	 *
	 * @param string $transaction_id
	 *
	 * @see https://admin.pay.nl/docpanel/api/Transaction/info/4
	 */
	public function transaction_info( $transaction_id ) {
		$result = null;

		// URL
		$url = $this->get_url( 'v4', 'Transaction', 'info', 'json', array(
			'token'         => $this->token,
			'transactionId' => $transaction_id,
		) );

		// Request
		$response = wp_remote_get( $url );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			$body = wp_remote_retrieve_body( $response );

			$data = json_decode( $body );

			$result = $this->parse_response( $data );
		}

		// Return result
		return $result;
	}

	//////////////////////////////////////////////////

	/**
	 * Get issuers
	 *
	 * @return array
	 */
	public function get_issuers() {
		$issuers = false;

		// URL
		$url = $this->get_url( 'v4', 'Transaction', 'getService', 'json', array(
			'token'           => $this->token,
			'serviceId'       => $this->service_id,
			'paymentMethodId' => Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods::IDEAL,
		) );

		// Request
		$response = wp_remote_get( $url );

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 == $response_code ) { // WPCS: loose comparison ok.
			$body = wp_remote_retrieve_body( $response );

			// NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
			$result = json_decode( $body );

			if ( null !== $result ) {
				$issuers = array();

				foreach ( $result->countryOptionList as $countries ) {
					foreach ( $countries->paymentOptionList as $payment_method ) {
						if ( Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods::IDEAL === $payment_method->id ) {
							foreach ( $payment_method->paymentOptionSubList as $issuer ) {
								$id   = Pronamic_WP_Pay_XML_Security::filter( $issuer->id );
								$name = Pronamic_WP_Pay_XML_Security::filter( $issuer->name );

								$issuers[ $id ] = $name;
							}
						}
					}
				}
			}
		}

		return $issuers;
	}
}
