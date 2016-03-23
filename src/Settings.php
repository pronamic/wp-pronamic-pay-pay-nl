<?php
/**
 * Title: Pay.nl gateway settings
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.4
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Settings extends Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	public function sections( array $sections ) {
		// Pay.nl
		$sections['pay-nl'] = array(
			'title'       => __( 'Pay.nl', 'pronamic_ideal' ),
			'methods'     => array( 'pay_nl' ),
			'description' => sprintf(
				__( 'Account details are provided by %s after registration. These settings need to match with the %1$s dashboard.', 'pronamic_ideal' ),
				__( 'Pay.nl', 'pronamic_ideal' )
			),
		);

		// Return sections
		return $sections;
	}

	public function fields( array $fields ) {
		// Token
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'pay-nl',
			'meta_key'    => '_pronamic_gateway_pay_nl_token',
			'title'       => __( 'Token', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'Token as mentioned at <strong>Merchant » Company data (Connection)</strong> in the payment provider dashboard.', 'pronamic_ideal' ),
		);

		// Service ID
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'pay-nl',
			'meta_key'    => '_pronamic_gateway_pay_nl_service_id',
			'title'       => __( 'Service ID', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'Service ID as mentioned at <strong>Manage » Services</strong> in the payment provider dashboard.', 'pronamic_ideal' ),
		);

		// Transaction feedback
		$fields[] = array(
			'section'     => 'pay-nl',
			'methods'     => array( 'pay_nl' ),
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'        => 'description',
			'html'        => sprintf(
				'<span class="dashicons dashicons-yes"></span> %s',
				__( 'Payment status updates will be processed without any additional configuration.', 'pronamic_ideal' )
			),
		);

		// Return fields
		return $fields;
	}
}
