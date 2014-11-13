<?php

/**
 * Title: Pay.nl error tests
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_ErrorTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test error
	 */
	public function test_error() {
		$error = new Pronamic_WP_Pay_Gateways_PayNL_Error( 'PAY-101', 'Location not found' );

		$expected = 'PAY-101 Location not found';

		$this->assertEquals( $expected, (string) $error );
	}
}
