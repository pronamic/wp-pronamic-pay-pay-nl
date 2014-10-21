<?php

/**
 * Title: Pay.nl utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Util {
	public static function get_ip_address() {
		if ( filter_has_var( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR' ) ) {
			return filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP );
		}

		if ( filter_has_var( INPUT_SERVER, 'REMOTE_ADDR' ) ) {
			return filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP );
		}

		return '127.0.0.1';
	}
}
