<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Gateways\PayNL\Error as PayNL_Error;
use stdClass;

/**
 * Title: Pay.nl client
 * Description:
 * Copyright: 2005-2023 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.4
 * @since   1.0.0
 */
class Client {
	/**
	 * API URL
	 *
	 * @var string
	 */
	const API_URL = 'https://rest-api.pay.nl/%s/%s/%s/%s/';

	/**
	 * Token.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Service id.
	 *
	 * @var string
	 */
	private $service_id;

	/**
	 * Construct and initialize an Pay.nl client
	 *
	 * @param string $token      Token.
	 * @param string $service_id Service ID.
	 */
	public function __construct( $token, $service_id ) {
		$this->token      = $token;
		$this->service_id = $service_id;
	}

	/**
	 * Get Pay.nl API URL.
	 *
	 * @param string $version    Version.
	 * @param string $namespace  Namespace.
	 * @param string $method     Method.
	 * @param string $output     Output.
	 *
	 * @return string
	 */
	private function get_url( $version, $namespace, $method, $output ) {
		return sprintf(
			self::API_URL,
			$version,
			$namespace,
			$method,
			$output
		);
	}

	/**
	 * Send request to the specified URL.
	 *
	 * @param string $version    Version.
	 * @param string $namespace  Namespace.
	 * @param string $method     Method.
	 * @param string $output     Output.
	 * @param array  $parameters Parameters.
	 *
	 * @return null|array|stdClass Response object or null if request failed.
	 */
	private function send_request( $version, $namespace, $method, $output, $parameters = [] ) {
		$response = \wp_remote_post(
			$this->get_url( $version, $namespace, $method, $output ),
			[
				'body' => $parameters,
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new \Exception(
				\sprintf(
					__( 'Unknown response from Pay.nl: "%s".', 'pronamic_ideal' ),
					$response->get_error_message()
				)
			);
		}

		// Body.
		$body = wp_remote_retrieve_body( $response );

		$result = json_decode( $body );

		// Result is array.
		if ( is_array( $result ) ) {
			return $result;
		}

		// Result is object
		// NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
		if ( ! is_object( $result ) ) {
			throw new \Exception( __( 'Unknown response from Pay.nl error.', 'pronamic_ideal' ) );
		}

		// Error.
		if ( isset( $result->request->errorId, $result->request->errorMessage ) && ! empty( $result->request->errorId ) ) {
			$pay_nl_error = new PayNL_Error( $result->request->errorId, $result->request->errorMessage );

			throw new \Exception( (string) $pay_nl_error );
		}

		// Check result (v3).
		if ( isset( $result->status, $result->error ) && ! filter_var( $result->status, FILTER_VALIDATE_BOOLEAN ) && ! empty( $result->error ) ) {
			throw new \Exception( $result->error );
		}

		// Check result (v4).
		if ( isset( $result->request, $result->request->result ) && '1' !== $result->request->result ) {
			throw new \Exception( __( 'Unknown Pay.nl error.', 'pronamic_ideal' ) );
		}

		// Return result.
		return $result;
	}

	/**
	 * Transaction start
	 *
	 * @param int    $amount        Transaction amount.
	 * @param string $ip_address    IP address.
	 * @param string $finish_url    Finish URL.
	 * @param array  $request_param Request parameters.
	 *
	 * @return null|stdClass
	 *
	 * @link https://admin.pay.nl/docpanel/api/Transaction/start/4
	 */
	public function transaction_start( $amount, $ip_address, $finish_url, $request_param = [] ) {
		$parameters = array_merge(
			$request_param,
			[
				'token'     => $this->token,
				'serviceId' => $this->service_id,
				'amount'    => $amount,
				'ipAddress' => $ip_address,
				'finishUrl' => $finish_url,
			]
		);

		// Request.
		$result = $this->send_request( 'v13', 'Transaction', 'start', 'json', $parameters );

		if ( is_array( $result ) ) {
			return null;
		}

		// Return result.
		return $result;
	}

	/**
	 * Transaction info.
	 *
	 * @param string $transaction_id Transaction ID.
	 *
	 * @link https://admin.pay.nl/docpanel/api/Transaction/info/4
	 *
	 * @return null|array|stdClass
	 */
	public function transaction_info( $transaction_id ) {
		// Request.
		$result = $this->send_request(
			'v13',
			'Transaction',
			'info',
			'json',
			[
				'token'         => $this->token,
				'transactionId' => $transaction_id,
			]
		);

		// Return result.
		return $result;
	}

	/**
	 * Get issuers.
	 *
	 * @return array<string, string>
	 */
	public function get_issuers() {
		// Request.
		$data = $this->send_request(
			'v13',
			'Transaction',
			'getBanks',
			'json'
		);

		if ( ! \is_array( $data ) ) {
			throw new \Exception( \__( 'Failed to request banks from Pay., received an unexpected answer from Pay.', 'pronamic_ideal' ) );
		}

		// Ok.
		$issuers = [];

		foreach ( $data as $item ) {
			$issuers[ $item->id ] = $item->name;
		}

		return $issuers;
	}
}
