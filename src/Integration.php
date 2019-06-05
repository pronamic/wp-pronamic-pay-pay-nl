<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Gateways\Common\AbstractIntegration;

/**
 * Title: Pay.nl integration
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Integration extends AbstractIntegration {
	public function __construct() {
		$this->id            = 'pay_nl';
		$this->name          = 'Pay.nl';
		$this->url           = 'https://www.pay.nl/';
		$this->product_url   = 'http://www.pay.nl/';
		$this->dashboard_url = 'https://admin.pay.nl/';
		$this->register_url  = 'https://www.pay.nl/registreren/?id=M-7393-3100';
		$this->provider      = 'pay_nl';
	}

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$fields = array();

		// Intro.
		$fields[] = array(
			'section' => 'general',
			'type'    => 'html',
			'html'    => sprintf(
				/* translators: 1: Pay.nl */
				__( 'Account details are provided by %1$s after registration. These settings need to match with the %1$s dashboard.', 'pronamic_ideal' ),
				__( 'Pay.nl', 'pronamic_ideal' )
			),
		);

		// Token.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_pay_nl_token',
			'title'    => __( 'Token', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'Token as mentioned at <strong>Merchant » Company data (Connection)</strong> in the payment provider dashboard.', 'pronamic_ideal' ),
		);

		// Service ID.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_pay_nl_service_id',
			'title'    => __( 'Service ID', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'Service ID as mentioned at <strong>Manage » Services</strong> in the payment provider dashboard.', 'pronamic_ideal' ),
		);

		// Return fields.
		return $fields;
	}

	public function get_config( $post_id ) {
		$config = new Config();

		$config->token      = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_token', true );
		$config->service_id = get_post_meta( $post_id, '_pronamic_gateway_pay_nl_service_id', true );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		return new Gateway( $this->get_config( $post_id ) );
	}
}
