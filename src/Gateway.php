<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\PaymentMethodsCollection;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Refunds\Refund;

/**
 * Title: Pay.nl gateway
 * Description:
 * Copyright: 2005-2026 Pronamic
 * Company: Pronamic
 *
 * @version 4.0.0
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Config.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Client.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructs and initializes an Pay.nl gateway
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->config = $config;

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = [
			'payment_status_request',
			'refunds',
		];

		// Client.
		$this->client = new Client( $this->config );

		// Methods.
		$this->register_payment_method( new PaymentMethod( PaymentMethods::AFTERPAY_NL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANCONTACT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANK_TRANSFER ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::CREDIT_CARD ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::FOCUM ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::GIROPAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::IDEAL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::IN3 ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::KLARNA_PAY_LATER ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MAESTRO ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::PAYPAL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::RIVERTY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SOFORT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SPRAYPAY ) );
        $this->register_payment_method(new PaymentMethod(PaymentMethods::VISA));
        $this->register_payment_method(new PaymentMethod(PaymentMethods::MASTERCARD));
        $this->register_payment_method(new PaymentMethod(PaymentMethods::AMERICAN_EXPRESS));

	}

    /**
     * Get payment methods.
     *
     * @param array<string, string> $args Query arguments.
     * @return PaymentMethodsCollection
     */
    public function get_payment_methods( array $args = [] ): PaymentMethodsCollection {
        try {
            $this->maybe_enrich_payment_methods();
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch -- No problem.
            // No problem.
        }

        return parent::get_payment_methods( $args );
    }


    /**
     * Maybe enrich payment methods.
     *
     * @return void
     */
    private function maybe_enrich_payment_methods() {
        $cache_key = 'pronamic_pay_pay_payment_methods_' . \md5( (string) \wp_json_encode( $this->config ) );
        $pay_payment_methods = \get_transient( $cache_key );

        if ( false === $pay_payment_methods ) {
            $pay_payment_methods = $this->client->get_payment_methods();
            \set_transient( $cache_key, $pay_payment_methods, \DAY_IN_SECONDS );
        }
//
        foreach ( $this->payment_methods as $payment_method ) {
            $pay_payment_method = Methods::transform($payment_method->get_id());
            $core_payment_method = $this->get_payment_method($payment_method->get_id());

            if(array_key_exists($pay_payment_method, $pay_payment_methods)) {
                $core_payment_method->set_status('active');
            } else {
                $core_payment_method->set_status('inactive');
            }
        }

        return;
    }

	/**
	 * Get customer request data.
	 *
	 * @param Payment $payment Payment.
	 * @return array<string, mixed>
	 */
	private function get_request_customer( Payment $payment ) {
		$customer = $payment->get_customer();

		if ( null === $customer ) {
			return [];
		}

		$data = [];

		if ( null !== $customer->get_gender() ) {
			$data['gender'] = $customer->get_gender();
		}

		if ( null !== $customer->get_phone() ) {
			$data['phone'] = $customer->get_phone();
		}

		if ( null !== $customer->get_email() ) {
			$data['email'] = $customer->get_email();
		}

		if ( null !== $customer->get_language() ) {
			$data['language'] = $customer->get_language();
		}

		$name = $customer->get_name();

		if ( null !== $name ) {
			if ( null !== $name->get_first_name() ) {
				$data['firstName'] = \mb_substr( (string) $name->get_first_name(), 0, 32 );
			}

			if ( null !== $name->get_last_name() ) {
				$data['lastName'] = \mb_substr( (string) $name->get_last_name(), 0, 32 );
			}
		}

		$birth_date = $customer->get_birth_date();

		if ( $birth_date instanceof \DateTimeInterface ) {
			$data['birthDate'] = $birth_date->format( 'Y-m-d' );
		}

		return $data;
	}

	/**
	 * Get address request data.
	 *
	 * @param \Pronamic\WordPress\Pay\Address|null $address Address.
	 * @return array<string, mixed>|null
	 */
	private function get_request_address( $address ) {
		if ( null === $address ) {
			return null;
		}

		$data = [];

		if ( null !== $address->get_street_name() ) {
			$data['street'] = $address->get_street_name();
		}

		if ( null !== $address->get_house_number_base() ) {
			$data['streetNumber'] = $address->get_house_number_base();
		}

		if ( null !== $address->get_house_number_addition() ) {
			$data['streetNumberExtension'] = $address->get_house_number_addition();
		}

		if ( null !== $address->get_postal_code() ) {
			$data['zipCode'] = $address->get_postal_code();
		}

		if ( null !== $address->get_city() ) {
			$data['city'] = $address->get_city();
		}

		if ( null !== $address->get_country_code() ) {
			$data['country'] = $address->get_country_code();
		}

		return $data;
	}

	/**
	 * Get order request data.
	 *
	 * @param Payment $payment Payment.
	 * @return array<string, mixed>
	 */
	private function get_request_order( Payment $payment ) {
		$order = [
			'invoiceDate'  => $payment->get_date()->format( 'd-m-Y' ),
			'deliveryDate' => $payment->get_date()->format( 'd-m-Y' ),
		];

		$delivery_address = $this->get_request_address( $payment->get_shipping_address() );

		if ( null !== $delivery_address ) {
			$order['deliveryAddress'] = $delivery_address;
		}

		$invoice_address = $this->get_request_address( $payment->get_billing_address() );

		if ( null !== $invoice_address ) {
			$order['invoiceAddress'] = $invoice_address;
		}

		$payment_lines = $payment->get_lines();

		if ( null !== $payment_lines ) {
			$order['products'] = [];

			foreach ( $payment_lines as $line ) {
				$product = [
					'id'          => $line->get_id(),
					'type'        => ProductTypes::transform( $line->get_type() ),
					'description' => $line->get_name(),
				];

				$quantity = $line->get_quantity();

				if ( null === $quantity ) {
					$product['quantity'] = 1;
				} elseif ( $quantity->is_whole_number() ) {
					$product['quantity'] = $quantity->to_int();
				} else {
					$product['quantity'] = (float) $quantity->get_value();
				}

				$unit_price = $line->get_unit_price();

				if ( null !== $unit_price ) {
					$product['price'] = [
						'value'    => $unit_price->get_minor_units()->to_int(),
						'currency' => $unit_price->get_currency()->get_alphabetic_code(),
					];
				}

				$order['products'][] = $product;
			}
		}

		return $order;
	}

	/**
	 * Start.
	 *
	 * @see Core_Gateway::start()
	 *
	 * @param Payment $payment Payment.
	 * @throws \Exception Throws exception when creating the order at Pay.nl fails.
	 */
	public function start( Payment $payment ) {
		/**
		 * Request.
		 *
		 * @link https://developer.pay.nl/reference/api_create_order-1
		 */
		$request = [
			'serviceId' => $this->config->service_id,
			'amount'    => [
				'value'    => $payment->get_total_amount()->get_minor_units()->to_int(),
				'currency' => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
			],
			'returnUrl' => $payment->get_return_url(),
			'customer'  => $this->get_request_customer( $payment ),
			'order'     => $this->get_request_order( $payment ),
			'stats'     => [
				/**
				 * Info.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The used info code which can be tracked in the stats."
				 */
				'info'   => 'Pronamic Pay payment ' . $payment->get_id(),
				/**
				 * Tool.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The used tool code which can be tracked in the stats."
				 */
				'tool'   => 'Pronamic Pay ' . \pronamic_pay_plugin()->get_version(),
				/**
				 * Extra 1.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The first free value which can be tracked in the stats."
				 */
				'extra1' => $payment->get_id(),
				/**
				 * Extra 2.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The second free value which can be tracked in the stats."
				 */
				'extra2' => \get_current_user_id(),
				/**
				 * Extra 3.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The third free value which can be tracked in the stats."
				 */
				'extra3' => $payment->get_source() . ' - ' . $payment->get_source_id(),
				/**
				 * Object.
				 *
				 * From https://developer.pay.nl/reference/api_create_order-1:
				 * > "The object which can be tracked in stats."
				 *
				 * From https://docs.pay.nl/developers#mandatory-data-technical-partners:
				 * > "Naam van het platform of de technische partner, eventueel gevolgd door een pipeline met versienummers"
				 */
				'object' => implode(
					' | ',
					[
						/**
						 * Pronamic Pay version.
						 *
						 * @link https://github.com/pronamic/pronamic-pay/issues/12
						 */
						'PronamicPay/' . \pronamic_pay_plugin()->get_version(),
						/**
						 * WordPress version.
						 *
						 * @link https://github.com/WordPress/WordPress/blob/f9db66d504fc72942515f6c0ed2b63aee7cef876/wp-includes/class-wp-http.php#L183-L192
						 */
						'WordPress/' . get_bloginfo( 'version' ),
					]
				),
			],
		];

		// Payment method.
		$method = Methods::transform( $payment->get_payment_method() );

		if ( null !== $method ) {
			$request['paymentMethod'] = [
				'id' => \intval( $method ),
			];
		}

		// Description (maximum of 32 characters).
		$description = $payment->get_description();

		if ( null !== $description && '' !== $description ) {
			$request['description'] = \mb_substr( $description, 0, 32 );
		}

		// Create order.
		$result = $this->client->create_order( $request );

		// Update gateway results in payment.
		if ( isset( $result->orderId ) ) {
			$payment->set_transaction_id( $result->orderId );
		}

		if ( isset( $result->links->redirect ) ) {
			$payment->set_action_url( $result->links->redirect );
		} elseif ( isset( $result->links->checkout ) ) {
			$payment->set_action_url( $result->links->checkout );
		}
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 */
	public function update_status( Payment $payment ) {
		$transaction_id = $payment->get_transaction_id();

		if ( empty( $transaction_id ) ) {
			return;
		}

		try {
			// Get order status.
			$result = $this->client->get_order_status( $transaction_id );
		} catch ( \Exception $e ) {
			return;
		}

		if ( ! is_object( $result ) || ! isset( $result->status->code ) ) {
			return;
		}

		$status = Statuses::transform( $result->status->code );

		if ( null === $status ) {
			return;
		}

		// Update payment status.
		$payment->set_status( $status );
	}

	/**
	 * Create refund.
	 *
	 * @param Refund $refund Refund.
	 * @return void
	 * @throws \Exception Throws exception when creating the refund at Pay.nl fails.
	 *
	 * @link https://developer.pay.nl/reference/patch_transactions-transactionid-refund
	 */
	public function create_refund( Refund $refund ) {
		$payment        = $refund->get_payment();
		$transaction_id = $payment->get_transaction_id();

		if ( empty( $transaction_id ) ) {
			throw new \Exception(
				__( 'Unable to create refund for payment without transaction ID.', 'pronamic_ideal' )
			);
		}

		$amount = $refund->get_amount();

		// Refund request.
		$request = [
			'amount' => [
				'value'    => $amount->get_minor_units()->to_int(),
				'currency' => $amount->get_currency()->get_alphabetic_code(),
			],
		];

		$description = $refund->get_description();

		if ( '' !== $description ) {
			$request['description'] = $description;
		}

		$result = $this->client->create_refund( $transaction_id, $request );

		// Check refund result.
		if ( ! is_object( $result ) || isset( $result->title ) ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: transaction ID */
					__( 'Unable to create refund for transaction with transaction ID: %s', 'pronamic_ideal' ),
					$transaction_id
				)
			);
		}

		// Refund ID.
		if ( isset( $result->refundedTransactions[0]->refund->id ) ) {
			$refund->psp_id = $result->refundedTransactions[0]->refund->id;
		}

		// Update payment refunded amount.
		if ( isset( $result->amountRefunded->value, $result->amountRefunded->currency ) ) {
			$refunded_amount = new Money(
				$result->amountRefunded->value / 100,
				$result->amountRefunded->currency
			);

			$payment->set_refunded_amount( $refunded_amount );
		}
	}
}
