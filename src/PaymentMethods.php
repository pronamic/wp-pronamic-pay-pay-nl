<?php

/**
 * Title: Pay.nl payment methods
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 1.0.0
 * @see https://admin.pay.nl/data/payment_profiles
 */
class Pronamic_WP_Pay_Gateways_PayNL_PaymentMethods {
	/**
	 * Constant for the iDEAL method.
	 *
	 * @var string
	 */
	const IDEAL = '10';

	/**
	 * Constant for the Credit Card method.
	 *
	 * @var string
	 */
	const CREDITCARD = '706';

	/**
	 * Constant for the Mister Cash method.
	 *
	 * @var string
	 */
	const MISTERCASH = '436';

	/**
	 * Constant for the Bank transfer method.
	 *
	 * @var string
	 */
	const BANKTRANSFER = '136';

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
}
