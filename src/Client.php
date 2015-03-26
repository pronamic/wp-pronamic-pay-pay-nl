<?php

/**
 * Title: Pay.nl client
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
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
			if ( 0 == $data->request->result && isset( $data->request->errorId, $data->request->errorMessage ) ) {
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
	public function transaction_start( $amount, $ip_address, $finish_url ) {
		$result = null;

		// URL
		$url = $this->get_url( 'v4', 'Transaction', 'start', 'json', array(
			'token'     => $this->token,
			'serviceId' => $this->service_id,
			'amount'    => Pronamic_WP_Util::amount_to_cents( $amount ),
			'ipAddress' => $ip_address,
			'finishUrl' => $finish_url,
		) );

		// Request
		$response = wp_remote_get( $url );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$body = wp_remote_retrieve_body( $response );

			$data = json_decode( $body );

			$result = $this->parse_response( $data );
		}

		// Return
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

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$body = wp_remote_retrieve_body( $response );

			$data = json_decode( $body );

			$result = $this->parse_response( $data );
		}

		// Return
		return $result;
	}
}
