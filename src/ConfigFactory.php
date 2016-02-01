<?php

/**
 * Title: Pay.nl config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_ConfigFactory extends Pronamic_WP_Pay_GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Pronamic_WP_Pay_Gateways_PayNL_Config();

		$config->token      = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_token', true );
		$config->service_id = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_service_id', true );

		return $config;
	}
}
