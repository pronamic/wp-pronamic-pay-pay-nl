<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\GatewayConfigFactory;

/**
 * Title: Pay.nl config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 */
class ConfigFactory extends GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Config();

		$config->token      = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_token', true );
		$config->service_id = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_service_id', true );

		return $config;
	}
}
