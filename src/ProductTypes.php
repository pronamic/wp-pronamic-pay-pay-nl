<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Payments\PaymentLineType;

/**
 * Product types.
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
 * @see     https://admin.pay.nl/docpanel/api
 */
class ProductTypes {
	/**
	 * Article.
	 *
	 * @var string
	 */
	const ARTICLE = 'ARTICLE';

	/**
	 * Shipping.
	 *
	 * @var string
	 */
	const SHIPPING = 'SHIPPING';

	/**
	 * Handling.
	 *
	 * @var string
	 */
	const HANDLING = 'HANDLING';

	/**
	 * Discount.
	 *
	 * @var string
	 */
	const DISCOUNT = 'DISCOUNT';

	/**
	 * Transform a Pronamic Pay payment line type to a Pay.nl product type.
	 *
	 * @param string $type
	 *
	 * @return null|string
	 */
	public static function transform( $type ) {
		switch ( $type ) {
			case PaymentLineType::DIGITAL:
				return self::ARTICLE;
			case PaymentLineType::DISCOUNT:
				return self::DISCOUNT;
			case PaymentLineType::PHYSICAL:
				return self::ARTICLE;
			case PaymentLineType::SHIPPING:
				return self::SHIPPING;
			default:
				return null;
		}
	}
}
