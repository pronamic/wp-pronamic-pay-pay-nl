<?php

/**
 * Title: Pay.nl output options
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @see https://admin.pay.nl/docpanel/api
 */
class Pronamic_WP_Pay_Gateways_PayNL_OutputOptions {
	/**
	 * Output XML
	 *
	 * @var string
	 */
	const OUTPUT_XML = 'xml';

	/**
	 * Output text
	 *
	 * @var string
	 */
	const OUPUT_TXT = 'txt';

	/**
	 * Output array
	 *
	 * @var string
	 */
	const OUTPUT_ARRAY = 'array';

	/**
	 * Output serialized array
	 *
	 * @var string
	 */
	const OUTPUT_ARRAY_SERIALIZE = 'array_serialize';

	/**
	 * Output JSON
	 *
	 * @var string
	 */
	const OUTPUT_JSON = 'json';

	/**
	 * Output JSONP
	 *
	 * @var string
	 */
	const OUTPUT_JSONP = 'jsonp';
}
