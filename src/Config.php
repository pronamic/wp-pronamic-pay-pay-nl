<?php
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: Pay.nl config
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Config extends GatewayConfig {
	public $token;

	public $service_id;

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_PayNL_Gateway';
	}
}
