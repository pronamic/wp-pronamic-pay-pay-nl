<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Pay.nl payment methods
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @see     https://admin.pay.nl/data/payment_profiles
 */
class Methods {
	/**
	 * Constant for the Bancontact method.
	 *
	 * @var string
	 */
	const BANCONTACT = '436';

	/**
	 * Constant for the Bank transfer method.
	 *
	 * @var string
	 */
	const BANKTRANSFER = '136';

	/**
	 * Constant for the Credit Card method.
	 *
	 * @var string
	 */
	const CREDITCARD = '706';

	/**
	 * Constant for the Giropay method.
	 *
	 * @var string
	 */
	const GIROPAY = '694';

	/**
	 * Constant for the iDEAL method.
	 *
	 * @var string
	 */
	const IDEAL = '10';

	/**
	 * Constant for the PayPal method.
	 *
	 * @var string
	 */
	const PAYPAL = '138';

	/**
	 * Constant for the Paysafecard method.
	 *
	 * @var string
	 */
	const PAYSAFECARD = '553';

	/**
	 * Constant for the Sofort method.
	 *
	 * @var string
	 */
	const SOFORT = '577';

	/**
	 * Payments methods map.
	 *
	 * @var array
	 */
	private static $map = array(
		PaymentMethods::BANCONTACT    => Methods::BANCONTACT,
		PaymentMethods::BANK_TRANSFER => Methods::BANKTRANSFER,
		PaymentMethods::CREDIT_CARD   => Methods::CREDITCARD,
		PaymentMethods::GIROPAY       => Methods::GIROPAY,
		PaymentMethods::IDEAL         => Methods::IDEAL,
		PaymentMethods::MISTER_CASH   => Methods::BANCONTACT,
		PaymentMethods::PAYPAL        => Methods::PAYPAL,
		PaymentMethods::SOFORT        => Methods::SOFORT,
	);

	/**
	 * Transform WordPress payment method to Pay.nl method.
	 *
	 * @param string $payment_method
	 *
	 * @return null|string
	 */
	public static function transform( $payment_method ) {
		if ( ! is_scalar( $payment_method ) ) {
			return null;
		}

		if ( isset( self::$map[ $payment_method ] ) ) {
			return self::$map[ $payment_method ];
		}

		return null;
	}
}
