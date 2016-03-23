<?php

/**
 * Title: Pay.nl integration
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.4
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Integration extends Pronamic_WP_Pay_Gateways_AbstractIntegration {
	public function __construct() {
		$this->id            = 'pay_nl';
		$this->name          = 'Pay.nl';
		$this->url           = 'https://www.pay.nl/';
		$this->product_url   = 'http://www.pay.nl/';
		$this->dashboard_url = 'https://admin.pay.nl/';
		$this->provider      = 'pay_nl';
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_ConfigFactory';
	}

	public function get_settings_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Settings';
	}

	/**
	 * Get required settings for this integration.
	 *
	 * @see https://github.com/wp-premium/gravityforms/blob/1.9.16/includes/fields/class-gf-field-multiselect.php#L21-L42
	 * @since 1.0.4
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		$settings[] = 'pay_nl';

		return $settings;
	}
}
