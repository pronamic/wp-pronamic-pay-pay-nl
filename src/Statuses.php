<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;

/**
 * Title: Pay.nl statuses
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Statuses {
	/**
	 * Paid
	 *
	 * @var string
	 *
	 * @link https://admin.pay.nl/docpanel/api/Transaction/info/4
	 */
	const PAID = '100';

	/**
	 * Cancelled.
	 *
	 * @var string
	 *
	 * @link https://plugins.trac.wordpress.org/browser/woocommerce-paynl-payment-methods/tags/2.2.6/includes/classes/Pay/Gateways.php#L180
	 */
	const CANCELLED = '-90';

	/**
	 * Transform an Pay.nl status to an Pronamic Pay status
	 *
	 * @param string $status
	 *
	 * @return null|string
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case self::PAID:
				return Core_Statuses::SUCCESS;

			case self::CANCELLED:
				return Core_Statuses::CANCELLED;

			default:
				return null;
		}
	}
}
