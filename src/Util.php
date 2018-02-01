<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Server;

/**
 * Title: Pay.nl utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.1.0
 * @since   1.0.0
 */
class Util {
	public static function get_ip_address() {
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return Server::get( 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP );
		}

		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return Server::get( 'REMOTE_ADDR', FILTER_VALIDATE_IP );
		}

		return '127.0.0.1';
	}
}
