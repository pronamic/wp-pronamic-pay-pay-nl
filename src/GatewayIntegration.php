<?php

class Pronamic_WP_Pay_Gateways_PayNL_GatewayIntegration {
	public function __construct() {
		$this->id = 'pay_nl';
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_ConfigFactory';
	}

	public function get_config_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Config';
	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Gateway';
	}
}
