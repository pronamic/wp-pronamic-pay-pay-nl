<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;

/**
 * Title: Pay.nl statuses
 * Description:
 * Copyright: 2005-2026 Pronamic
 * Company: Pronamic
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @link https://developer.pay.nl/docs/transaction-statuses
 */
class Statuses {
	/**
	 * Pending.
	 *
	 * @var int
	 *
	 * @link https://developer.pay.nl/docs/transaction-statuses#pending-statuses
	 */
	const PENDING = 20;

	/**
	 * Pending, data received and sent to the payment method.
	 *
	 * @var int
	 */
	const PENDING_DATA = 50;

	/**
	 * Pending, waiting for a synchronous response of the bank or third party.
	 *
	 * @var int
	 */
	const PENDING_BANK = 90;

	/**
	 * Pending, provider or issuer confirmed that the payment is send, but the funds are not received yet.
	 *
	 * @var int
	 */
	const PENDING_FUNDS = 98;

	/**
	 * Cancelled by user.
	 *
	 * @var int
	 */
	const CANCELLED = -90;

	/**
	 * Expired.
	 *
	 * @var int
	 */
	const EXPIRED = -80;

	/**
	 * Denied by user or via the API (transaction:decline).
	 *
	 * @var int
	 */
	const DENIED_BY_CONSUMER = -64;

	/**
	 * Denied by payment processor.
	 *
	 * @var int
	 */
	const DENIED_BY_PROCESSOR = -63;

	/**
	 * Voided after a successful authorization (orders API only).
	 *
	 * @var int
	 */
	const VOIDED = -61;

	/**
	 * Failure.
	 *
	 * @var int
	 */
	const FAILURE = -60;

	/**
	 * Paid check amount.
	 *
	 * @var int
	 */
	const CHECK_AMOUNT = -51;

	/**
	 * Partial payment, used with gift cards that do not cover the full amount of the order.
	 *
	 * @var int
	 */
	const PARTIAL_PAYMENT = 80;

	/**
	 * Verify, the payment is treated as suspicious by the verify module.
	 *
	 * @var int
	 */
	const VERIFY = 85;

	/**
	 * Authorized, reserved for capture (credit card payments and BNPL).
	 *
	 * @var int
	 */
	const AUTHORIZED = 95;

	/**
	 * Partly captured.
	 *
	 * @var int
	 */
	const PARTLY_CAPTURED = 97;

	/**
	 * Paid.
	 *
	 * @var int
	 */
	const PAID = 100;

	/**
	 * Transform a Pay.nl status to an Pronamic Pay status.
	 *
	 * @param int|string|null $status Status code.
	 *
	 * @return null|string
	 */
	public static function transform( $status ) {
		switch ( (int) $status ) {
			case self::PAID:
			case self::PARTLY_CAPTURED:
				return Core_Statuses::SUCCESS;

			case self::CANCELLED:
			case self::DENIED_BY_CONSUMER:
			case self::DENIED_BY_PROCESSOR:
			case self::VOIDED:
				return Core_Statuses::CANCELLED;

			case self::EXPIRED:
				return Core_Statuses::EXPIRED;

			case self::FAILURE:
				return Core_Statuses::FAILURE;

			case self::CHECK_AMOUNT:
			case self::VERIFY:
				return Core_Statuses::ON_HOLD;

			case self::AUTHORIZED:
				return Core_Statuses::AUTHORIZED;

			case self::PENDING:
			case self::PENDING_DATA:
			case self::PENDING_BANK:
			case self::PENDING_FUNDS:
			case self::PARTIAL_PAYMENT:
				return Core_Statuses::OPEN;

			default:
				return null;
		}
	}
}
