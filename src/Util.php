<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

use Pronamic\WordPress\Pay\Core\Util as Core_Util;

/**
 * Title: Pay.nl utility class
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Util {
	/**
	 * Get IP address.
	 *
	 * @return mixed
	 */
	public static function get_ip_address() {
		$ip_address = Core_Util::get_remote_address();

		if ( null !== $ip_address ) {
			return $ip_address;
		}

		return '127.0.0.1';
	}
}
