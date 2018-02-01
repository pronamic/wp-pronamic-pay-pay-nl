<?php

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;
use Pronamic\WordPress\Pay\Gateways\PayNL\Statuses;

/**
 * Title: Pay.nl states constants tests
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_StatusesTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider states_matrix_provider
	 */
	public function test_transform( $state, $expected ) {
		$status = Statuses::transform( $state );

		$this->assertEquals( $expected, $status );
	}

	public function states_matrix_provider() {
		return array(
			array( Statuses::PAID, Core_Statuses::SUCCESS ),
			array( Statuses::CANCELED, Core_Statuses::CANCELLED ),
			array( 'not existing status', null ),
		);
	}
}
