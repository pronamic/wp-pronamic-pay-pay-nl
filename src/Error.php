<?php

/**
 * Title: Pay.nl error
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.5
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_PayNL_Error {
	/**
	 * Pay.nl error ID
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Pay.nl error message
	 *
	 * @var string
	 */
	private $message;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Pay.nl error object
	 *
	 * @param string $id
	 * @param string $message
	 */
	public function __construct( $id, $message ) {
		$this->id      = $id;
		$this->message = $message;
	}

	//////////////////////////////////////////////////

	// @todo getters and setters

	//////////////////////////////////////////////////

	/**
	 * Create an string representation of this object
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->id . ' - ' . $this->message;
	}
}
