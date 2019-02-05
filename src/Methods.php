<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Pay.nl payment methods
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 2.0.1
 * @since   1.0.0
 * @see     https://admin.pay.nl/data/payment_profiles
 */
class Methods {
	/**
	 * Constant for the AfterPay method.
	 *
	 *  739 = AfterPay
	 *  740 = AfterPay EM
	 * 1921 = AfterPay NL B2B
	 * 1918 = AfterPay NL B2C
	 *
	 * @link https://admin.pay.nl/data/payment_profiles
	 *
	 * @var string
	 */
	const AFTERPAY = '739';

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
	 * Constant for the Billink < € 100 method.
	 *
	 * @var string
	 */
	const BILLINK_LOW = '1672';

	/**
	 * Constant for the Billink > € 100 method.
	 *
	 * @var string
	 */
	const BILLINK_HIGH = '1675';

	/**
	 * Constant for the Capayable Achteraf Betalen method.
	 *
	 * @var string
	 */
	const CAPAYABLE = '1744';

	/**
	 * Constant for the Credit Card method.
	 *
	 * @var string
	 */
	const CREDITCARD = '706';

	/**
	 * Constant for the Focum method.
	 *
	 * @var string
	 */
	const FOCUM = '1702';

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
	 * Constant for the In3 (Gesprek betalen) method.
	 *
	 * @var string
	 */
	const IN3 = '1813';

	/**
	 * Constant for the Klarna method.
	 *
	 * @var string
	 */
	const KLARNA_PAY_LATER = '1717';

	/**
	 * Constant for the Maestro method.
	 *
	 * @var string
	 */
	const MAESTRO = '712';

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
	 * Constant for the Sofort (Digital Services) method.
	 *
	 * 559 = Sofortbanking eCommerce (fysieke producten)
	 * 577 = Sofortbanking Digital services
	 * 595 = Sofortbanking High risk
	 *
	 * @link https://admin.pay.nl/data/payment_profiles
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
		PaymentMethods::AFTERPAY         => self::AFTERPAY,
		PaymentMethods::BANCONTACT       => self::BANCONTACT,
		PaymentMethods::BANK_TRANSFER    => self::BANKTRANSFER,
		PaymentMethods::CREDIT_CARD      => self::CREDITCARD,
		PaymentMethods::FOCUM            => self::FOCUM,
		PaymentMethods::GIROPAY          => self::GIROPAY,
		PaymentMethods::IDEAL            => self::IDEAL,
		PaymentMethods::IN3              => self::IN3,
		PaymentMethods::KLARNA_PAY_LATER => self::KLARNA_PAY_LATER,
		PaymentMethods::MISTER_CASH      => self::BANCONTACT,
		PaymentMethods::MAESTRO          => self::MAESTRO,
		PaymentMethods::PAYPAL           => self::PAYPAL,
		PaymentMethods::SOFORT           => self::SOFORT,
	);

	/**
	 * Transform WordPress payment method to Pay.nl method.
	 *
	 * @param mixed $payment_method Payment method.
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
