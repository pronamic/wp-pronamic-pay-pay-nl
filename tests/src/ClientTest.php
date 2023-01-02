<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use WP_UnitTestCase;
use WP_Http;

/**
 * Title: Pay.nl client test
 * Description:
 * Copyright: 2005-2023 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.4
 * @since   1.0.0
 */
class ClientTest extends \WP_UnitTestCase {
	/**
	 * Mock HTTP responses.
	 *
	 * @var array
	 */
	private $mock_http_responses;

	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->mock_http_responses = [];

		// Mock HTTP response.
		add_filter( 'pre_http_request', [ $this, 'pre_http_request' ], 10, 3 );
	}

	/**
	 * Mock HTTP response.
	 *
	 * @param string $url  URL.
	 * @param string $file File with HTTP response.
	 */
	public function mock_http_response( $url, $file ) {
		$this->mock_http_responses[ $url ] = $file;
	}

	/**
	 * Pre HTTP request
	 *
	 * @link https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 *
	 * @param false|array|\WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                 $r       HTTP request arguments.
	 * @param string                $url     The request URL.
	 *
	 * @return array
	 */
	public function pre_http_request( $preempt, $r, $url ) {
		if ( ! isset( $this->mock_http_responses[ $url ] ) ) {
			return $preempt;
		}

		$file = $this->mock_http_responses[ $url ];

		$response = file_get_contents( $file, true );

		$processed_response = WP_Http::processResponse( $response );

		$processed_headers = WP_Http::processHeaders( $processed_response['headers'], $url );

		$processed_headers['body'] = $processed_response['body'];

		return $processed_headers;
	}

	/**
	 * Test get issuers.
	 *
	 * @throws \Exception Throws exception if service can not be found.
	 */
	public function test_get_issuers() {
		$this->mock_http_response( 'https://rest-api.pay.nl/v4/Transaction/getService/json/?token&serviceId&paymentMethodId=10', dirname( __DIR__ ) . '/http/transaction-get-service-json-ideal-service-not-found.http' );

		$client = new Client( '', '' );

		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'PAY-404 - Service not found' );

		$client->get_issuers();
	}
}
