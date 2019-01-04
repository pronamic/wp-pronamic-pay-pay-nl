<?php

namespace Pronamic\WordPress\Pay\Gateways\PayNL;

/**
 * Title: Pay.nl output options
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 * @see     https://admin.pay.nl/docpanel/api
 */
class OutputOptions {
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
