<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use PHPUnit_Framework_TestCase;

/**
 * Title: Pay.nl error tests
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ErrorTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test error
	 */
	public function test_error() {
		$error = new Error( 'PAY-101', 'Location not found' );

		$expected = 'PAY-101 - Location not found';

		$this->assertEquals( $expected, (string) $error );
	}
}
