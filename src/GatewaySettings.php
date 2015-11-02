<?php
/**
 * Title: Pay.nl gateway settings
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.2.0
 * @since 1.2.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_GatewaySettings extends Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	public function sections( array $sections ) {
		// Pay.nl
		$sections['sisow'] = array(
			'title'   => __( 'Pay.nl', 'pronamic_ideal' ),
			'methods' => array( 'pay_nl' ),
		);

		// Return
		return $sections;
	}

	public function fields( array $fields ) {
		// Token
		$fields[] = array(
			'section'     => 'sisow',
			'meta_key'    => '_pronamic_gateway_pay_nl_token',
			'title'       => __( 'Token', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'description' => sprintf(
				__( 'You can find your token on the <a href="%s" target="_blank">Pay.nl admin page</a> under <a href="%s" target="_blank">Merchant » Company data (Connection)</a>.', 'pronamic_ideal' ),
				'https://admin.pay.nl/',
				'https://admin.pay.nl/my_merchant'
			),
		);

		// Service ID
		$fields[] = array(
			'section'     => 'sisow',
			'meta_key'    => '_pronamic_gateway_pay_nl_service_id',
			'title'       => __( 'Service ID', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'description' => sprintf(
				__( 'You can find your service ID on the <a href="%s" target="_blank">Pay.nl admin page</a> under <a href="%s" target="_blank">Manage » Services</a>.', 'pronamic_ideal' ),
				'https://admin.pay.nl/',
				'https://admin.pay.nl/programs/programs'
			),
		);

		// Return
		return $fields;
	}
}
