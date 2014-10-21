<?php

class Pronamic_WP_Pay_Gateways_PayNL_ClientTest extends PHPUnit_Framework_TestCase {
    public function test_start_transaction() {
    	$client = new Pronamic_WP_Pay_Gateways_PayNL_Client();

    	$client->start_transaction();
    }
}
