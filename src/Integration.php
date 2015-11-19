<?php

class Pronamic_WP_Pay_Gateways_PayNL_Integration extends Pronamic_WP_Pay_Gateways_AbstractIntegration {
	public function __construct() {
		$this->id            = 'pay_nl';
		$this->name          = 'Pay.nl';
		$this->url           = 'https://www.pay.nl/';
		$this->dashboard_url = 'https://www.pay.nl/';
		$this->provider      = 'pay_nl';
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_ConfigFactory';
	}

	public function get_config_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Config';
	}

	public function get_settings_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Settings';
	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Gateway';
	}
}
