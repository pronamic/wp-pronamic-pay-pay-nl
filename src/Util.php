<?php

/**
 * Title: Pay.nl utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Util {
	public static function get_ip_address() {
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return Pronamic_WP_Pay_Server::get( 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP );
		}

		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return Pronamic_WP_Pay_Server::get( 'REMOTE_ADDR', FILTER_VALIDATE_IP );
		}

		return '127.0.0.1';
	}
}
