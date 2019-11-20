<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use WP_UnitTestCase;
use WP_Http;

/**
 * Title: Pay.nl client test
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ClientTest extends \WP_UnitTestCase {
	/**
	 * Pre HTTP request
	 *
	 * @link https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 * @return array
	 */
	public function pre_http_request( $preempt, $request, $url ) {
		$response = file_get_contents( dirname( dirname( __FILE__ ) ) . '/Mock/transaction-get-service-json-ideal-service-not-found.http', true );

		$processed_response = WP_Http::processResponse( $response );

		$processed_headers = WP_Http::processHeaders( $processed_response['headers'], $url );

		$processed_headers['body'] = $processed_response['body'];

		return $processed_headers;
	}

	public function test_get_issuers() {
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

		$client = new Client( '', '' );

		$issuers = $client->get_issuers();

		$this->assertFalse( $issuers );
		$this->assertInstanceOf( 'WP_Error', $client->get_error() );
	}
}
