<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use JsonSerializable;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: Pay.nl config
 * Description:
 * Copyright: 2005-2026 Pronamic
 * Company: Pronamic
 *
 * @version 3.0.0
 * @since   1.0.0
 */
class Config extends GatewayConfig implements JsonSerializable {
	/**
	 * Token code (AT-…), used as username for HTTP Basic authentication.
	 *
	 * @var string
	 */
	public $token_code;

	/**
	 * API token, used as password for HTTP Basic authentication.
	 *
	 * @var string
	 */
	public $token;

	/**
	 * Service ID (SL-…).
	 *
	 * @var string
	 */
	public $service_id;

	/**
	 * Serialize to JSON.
	 *
	 * @return object
	 */
	public function jsonSerialize(): object {
		return (object) [
			'@type'      => __CLASS__,
			'token_code' => $this->token_code,
			'token'      => $this->token,
			'service_id' => $this->service_id,
		];
	}
}
