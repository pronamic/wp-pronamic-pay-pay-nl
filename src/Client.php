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
	const API_URL = 'https://rest-api.pay.nl/%s/%s/%s/%s/';

	/////////////////////////////////////////////////

	/**
	 * Construct and initialize an Pay.nl client
	 *
	 * @param string $token
	 * @param string $service_id
	 */
	public function __construct( $token, $service_id ) {
		$this->token = $token;
		$this->service_id = $service_id;
	}

	/////////////////////////////////////////////////

	public function get_urL( $version, $namespace, $method, $output, $parameters = array() ) {
		return add_query_arg( $parameters, sprintf(
			self::API_URL,
			$version,
			$namespace,
			$method,
			$output
		) );
	}

	public function start_transaction( $amount, $ip_address, $finish_url ) {
		$url = $this->get_url( 'v4', 'Transaction', 'start', 'json', array(
			'token'     => $this->token,
			'serviceId' => $this->service_id,
			'amount'    => Pronamic_WP_Util::amount_to_cents( $amount ),
			'ipAddress'	=> $ip_address,
			'finishUrl' => $finish_url,
		) );


		$result = wp_remote_get( $url );

		if ( 200 == wp_remote_retrieve_response_code( $result ) ) {
			$body = wp_remote_retrieve_body( $result );

			$data = json_decode( $body );

			if ( isset( $data, $data->request, $data->request->result ) ) {
				var_dump( $data );
			}
		}
	}
}
