<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

/**
 * Title: Pay.nl client
 * Description:
 * Copyright: 2005-2026 Pronamic
 * Company: Pronamic
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @link https://developer.pay.nl/docs/orders-1
 */
class Client {
	/**
	 * Connect API URL.
	 *
	 * @var string
	 */
	const CONNECT_API_URL = 'https://connect.pay.nl/v1/';

	/**
	 * REST API URL.
	 *
	 * @var string
	 */
	const REST_API_URL = 'https://rest.pay.nl/v2/';

    /**
     * The config for the client
     *
     * @var Config
     */
    private $config;
	/**
	 * Token code.
	 *
	 * @var string
	 */
	private $token_code;

	/**
	 * API token.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Construct and initialize an Pay.nl client.
	 *
	 * @param Config $config     the configuration
	 */
	public function __construct( $config ) {
		$this->config = $config;
		$this->token_code = $config->token_code;
		$this->token      = $config->token;
	}

	/**
	 * Get authorization header value.
	 *
	 * Authentication occurs via HTTP Basic authentication using:
	 *
	 * - Token code (AT-…) as username and API token as password, or
	 * - Service ID (SL-…) as username and secret as password.
	 *
	 * @return string
	 *
	 * @link https://developer.pay.nl/docs/create-an-order#12-authentication
	 */
	private function get_authorization_header() {
		return 'Basic ' . base64_encode( $this->token_code . ':' . $this->token ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Send request.
	 *
	 * @param string                          $method HTTP method.
	 * @param string                          $url    URL.
	 * @param object|array<string, mixed>|null $data  Data.
	 * @return object
	 * @throws \Exception Throws exception when the request fails.
	 */
	private function send_request( $method, $url, $data = null ) {
		$args = [
			'method'  => $method,
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => $this->get_authorization_header(),
				'Content-Type'  => 'application/json',
			],
		];

		if ( null !== $data && $method !== 'GET' ) {
			$args['body'] = \wp_json_encode( $data );
		}

        if(null !== $data && $method === 'GET') {
            $url = add_query_arg($data, $url);
        }

		try {
			$response = \Pronamic\WordPress\Http\Facades\Http::request( $url, $args );
		} catch ( \Exception $e ) {
			throw new \Exception(
				\sprintf(
					/* translators: %s: error message */
					__( 'Request to Pay.nl failed: "%s".', 'pronamic_ideal' ),
					$e->getMessage()
				)
			);
		}

		// Body.
		$body = $response->body();

		$result = \json_decode( $body );

		// NULL is returned if the JSON cannot be decoded.
		if ( ! is_object( $result ) ) {
			throw new \Exception(
				\sprintf(
					/* translators: %s: HTTP status code */
					__( 'Unknown response from Pay.nl with status code %s.', 'pronamic_ideal' ),
					$response->status()
				)
			);
		}

		// Error.
		if ( isset( $result->title ) || isset( $result->detail ) ) {
			throw new \Exception( $this->parse_error_message( $result ) );
		}

		if ( $response->status() >= 400 ) {
			throw new \Exception(
				\sprintf(
					/* translators: %s: HTTP status code */
					__( 'Error response from Pay.nl with status code %s.', 'pronamic_ideal' ),
					$response->status()
				)
			);
		}

		return $result;
	}

	/**
	 * Parse error message from result object.
	 *
	 * @param object $result Result.
	 * @return string
	 *
	 * @link https://developer.pay.nl/docs/error-codes
	 */
	private function parse_error_message( $result ) {
		$message = '';

		if ( \property_exists( $result, 'detail' ) && null !== $result->detail ) {
			$message = $result->detail;
		} elseif ( \property_exists( $result, 'title' ) && null !== $result->title ) {
			$message = $result->title;
		}

		if ( \property_exists( $result, 'violations' ) && is_array( $result->violations ) ) {
			foreach ( $result->violations as $violation ) {
				$message .= ' ' . $violation->message;

				if ( \property_exists( $violation, 'propertyPath' ) && null !== $violation->propertyPath ) {
					$message .= ' (' . $violation->propertyPath . ')';
				}
			}
		}

		return (string) $message;
	}

	/**
	 * Create order.
	 *
	 * @param array<string, mixed> $order Order.
	 * @return object
	 * @throws \Exception Throws exception when the request fails.
	 *
	 * @link https://developer.pay.nl/reference/api_create_order-1
	 */
	public function create_order( array $order ) {
		return $this->send_request( 'POST', self::CONNECT_API_URL . 'orders', $order );
	}

	/**
	 * Get order status.
	 *
	 * @param string $order_id Order ID.
	 * @return object
	 * @throws \Exception Throws exception when the request fails.
	 *
	 * @link https://developer.pay.nl/reference/api_get_status-1
	 */
	public function get_order_status( $order_id ) {
		return $this->send_request( 'GET', self::CONNECT_API_URL . 'orders/' . rawurlencode( (string) $order_id ) . '/status' );
	}

	/**
	 * Create refund.
	 *
	 * The transaction ID can be an EX code or a Pay. order ID.
	 *
	 * @param string                    $transaction_id Transaction ID.
	 * @param array<string, mixed>      $refund         Refund.
	 * @return object
	 * @throws \Exception Throws exception when the request fails.
	 *
	 * @link https://developer.pay.nl/reference/patch_transactions-transactionid-refund
	 */
	public function create_refund( $transaction_id, array $refund ) {
		return $this->send_request( 'PATCH', self::REST_API_URL . 'transactions/' . rawurlencode( (string) $transaction_id ) . '/refund', $refund );
	}

    /**
     * Get the service configuration for this account
     *
     * @return array
     */
    public function get_service_config()
    {
        return $this->send_request('GET', self::REST_API_URL . 'services/config', [
            'serviceId' => $this->config->service_id
        ]);
    }

    /**
     * Get all the payment methods
     *
     * @return array
     */
    public function get_payment_methods(): array
    {
        $data = (array) $this->get_service_config();
        $paymentMethods = [];
        foreach ($data['checkoutOptions'] ?? [] as $checkoutOption) {
            foreach ($checkoutOption->paymentMethods ?? [] as $paymentMethod) {
                $paymentMethods[$paymentMethod->id] = [
                    'id' => $paymentMethod->id,
                    'name' => $paymentMethod->name,
                    'description' => $paymentMethod->description ?? null,
                    'image' => $paymentMethod->image ?? null,
                    'minAmount' => $paymentMethod->minAmount ?? null,
                    'maxAmount' => $paymentMethod->maxAmount ?? null,
                    'targetCountries' => $paymentMethod->targetCountries ?? [],
                ];
            }
        }

        return $paymentMethods;
    }
}
