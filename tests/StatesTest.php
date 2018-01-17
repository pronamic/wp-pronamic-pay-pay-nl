<?php

/**
 * Title: Pay.nl states constants tests
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_StatesTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider states_matrix_provider
	 */
	public function test_transform( $state, $expected ) {
		$status = Pronamic_WP_Pay_Gateways_PayNL_States::transform( $state );

		$this->assertEquals( $expected, $status );
	}

	public function states_matrix_provider() {
		return array(
			array( Pronamic_WP_Pay_Gateways_PayNL_States::PAID, Pronamic_WP_Pay_Statuses::SUCCESS ),
			array( Pronamic_WP_Pay_Gateways_PayNL_States::CANCELED, Pronamic_WP_Pay_Statuses::CANCELLED ),
			array( 'not existing status', null ),
		);
	}
}
