<?php

/**
 * Title: Pay.nl states
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_States {
	/**
	 * Paid
	 *
	 * @var string
	 *
	 * @see https://admin.pay.nl/docpanel/api/Transaction/info/4
	 */
	const PAID = '100';

	/**
	 * Canceled
	 *
	 * @var string
	 *
	 * @see https://plugins.trac.wordpress.org/browser/woocommerce-paynl-payment-methods/tags/2.2.6/includes/classes/Pay/Gateways.php#L180
	 */
	const CANCELED = '-90';

	/////////////////////////////////////////////////

	/**
	 * Transform an Pay.nl status to an Pronamic Pay status
	*
	* @param string $status
	*/
	public static function transform( $state ) {
		switch ( $state ) {
			case self::PAID :
				return Pronamic_WP_Pay_Statuses::SUCCESS;
			case self::CANCELED :
				return Pronamic_WP_Pay_Statuses::CANCELLED;
			default:
				return null;
		}
	}
}
