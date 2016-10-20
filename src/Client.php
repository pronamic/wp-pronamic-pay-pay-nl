<?php

/**
 * Title: Pay.nl client
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.7
 * @since 1.0.0
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

	/**
	 * Send request to the specified URL.
	 *
	 * @param string $url
	 * @return stdClass response object or false if request failed.
	 */
	private function send_request( $version, $namespace, $method, $output, $parameters = array() ) {
		$url = $this->get_url( $version, $namespace, $method, $output, $parameters );

		$response = wp_remote_get( $url );

		// Body
		$body = wp_remote_retrieve_body( $response );

		$result = json_decode( $body );

		// Result is array
		if ( is_array( $result ) ) {
			return $result;
		}

		// Result is object
		// NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
		if ( ! is_object( $result ) ) {
			$this->error = new WP_Error(
				'unknown_response',
				__( 'Unknown response from Pay.nl error.', 'pronamic_ideal' ),
				$result
			);

			return null;
		}

		// Error
		if ( isset( $result->request->errorId, $result->request->errorMessage ) && ! empty( $result->request->errorId ) ) {
			$pay_nl_error = new Pronamic_WP_Pay_Gateways_PayNL_Error( $result->request->errorId, $result->request->errorMessage );

			$this->error = new WP_Error(
				'pay_nl_error',
				(string) $pay_nl_error,
				$pay_nl_error
			);

			return null;
		}

		// Check result (v3)
		if ( isset( $result->status, $result->error ) && ! filter_var( $result->status, FILTER_VALIDATE_BOOLEAN ) && ! empty( $result->error ) ) {
			$this->error = new WP_Error(
				'pay_nl_error',
				$result->error,
				$result
			);

			return null;
		}

		// Check result (v4)
		if ( isset( $result->request, $result->request->result ) && '1' !== $result->request->result ) {
			$this->error = new WP_Error(
				'pay_nl_error',
				__( 'Unknown Pay.nl error.', 'pronamic_ideal' ),
				$result
			);

			return null;
		}

		// Return result
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

		// Request
		$result = $this->send_request( 'v4', 'Transaction', 'start', 'json', $parameters );

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
		// Request
		$result = $this->send_request( 'v4', 'Transaction', 'info', 'json', array(
			'token'         => $this->token,
			'transactionId' => $transaction_id,
		) );

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
		// Request
		$result = $this->send_request( 'v4', 'Transaction', 'getService', 'json', array(
			'token'           => $this->token,
			'serviceId'       => $this->service_id,
			'paymentMethodId' => Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods::IDEAL,
		) );

		if ( ! $result ) {
			return false;
		}

		// Country option list
		if ( ! isset( $result->countryOptionList ) ) {
			$this->error = new WP_Error(
				'pay_nl_error',
				__( 'Unknown Pay.nl error.', 'pronamic_ideal' ),
				$result
			);

			return false;
		}

		// Ok
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

		return $issuers;
	}
}
