<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Pay.nl integration
 * Description:
 * Copyright: 2005-2023 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.4
 * @since   1.0.0
 */
class Integration extends AbstractGatewayIntegration {
	/**
	 * Construct Pay.nl integration.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'            => 'pay_nl',
				'name'          => 'Pay.',
				'url'           => 'https://www.pay.nl/',
				'product_url'   => 'https://www.pay.nl/',
				'dashboard_url' => 'https://my.pay.nl/',
				'register_url'  => 'https://www.pay.nl/registreren/?id=M-7393-3100',
				'provider'      => 'pay_nl',
				'manual_url'    => \__( 'https://www.pronamicpay.com/en/manuals/how-to-connect-pay-nl-to-wordpress-with-pronamic-pay/', 'pronamic_ideal' ),
			]
		);

		parent::__construct( $args );

		add_filter( 'pronamic_payment_provider_url_pay_nl', [ $this, 'payment_provider_url' ], 10, 2 );
	}

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$fields = [];

		// Token.
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_pay_nl_token',
			'title'    => \__( 'Token', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'tooltip'  => \__( 'Token as mentioned at <strong>Merchant → API Tokens</strong> in the Pay. dashboard.', 'pronamic_ideal' ),
		];

		// Service ID.
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_pay_nl_service_id',
			'title'    => \__( 'Sales location code', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'tooltip'  => \__( 'Sales location code as mentioned at <strong>Settings → Sales locations</strong> in the Pay. dashboard.', 'pronamic_ideal' ),
		];

		// Return fields.
		return $fields;
	}

	/**
	 * Payment provider URL.
	 *
	 * @param string|null $url     Payment provider URL.
	 * @param Payment     $payment Payment.
	 * @return string|null
	 */
	public function payment_provider_url( ?string $url, Payment $payment ): ?string {
		$transaction_id = $payment->get_transaction_id();

		if ( null === $transaction_id ) {
			return $url;
		}

		return sprintf(
			'https://my.pay.nl/transactions/details/%s',
			$transaction_id
		);
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
