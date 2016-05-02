<?php

/**
 * Title: Pay.nl client test
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_ClientTest extends WP_UnitTestCase {
	/**
	 * Pre HTTP request
	 *
	 * @see https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 * @return string
	 */
	public function pre_http_request( $preempt, $request, $url ) {
		$response = file_get_contents( dirname( __FILE__ ) . '/mocks/transaction-get-service-json-ideal-service-not-found.http' );

		$processedResponse = WP_Http::processResponse( $response );

		$processedHeaders = WP_Http::processHeaders( $processedResponse['headers'], $url );
		$processedHeaders['body'] = $processedResponse['body'];

		return $processedHeaders;
	}

	public function test_get_issuers() {
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

		$client = new Pronamic_WP_Pay_Gateways_PayNL_Client( '', '' );

		$issuers = $client->get_issuers();

		$this->assertFalse( $issuers );
		$this->assertInstanceOf( 'WP_Error', $client->get_error() );
	}
}
