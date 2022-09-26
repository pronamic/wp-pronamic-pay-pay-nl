<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use JsonSerializable;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: Pay.nl config
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Config extends GatewayConfig implements JsonSerializable {
	public $token;

	public $service_id;

	/**
	 * Serialize to JSON.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'@type'      => __CLASS__,
			'token'      => $this->token,
			'service_id' => $this->service_id,
		];
	}
}
