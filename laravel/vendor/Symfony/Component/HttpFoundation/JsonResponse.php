<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * Response represents an HTTP response in JSON format.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class JsonResponse extends Response
{
    /**
     * Constructor.
     *
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     */
    public function __construct($data = array(), $status = 200, $headers = array())
    {
        // root should be JSON object, not array
        if (is_array($data) && 0 === count($data)) {
            $data = new \ArrayObject();
        }

		// JSON_NUMERIC_CHECK is only defined in PHP 5.3.3, but we
		// add it if it is available
		$flags = 0;
		if (defined('JSON_NUMERIC_CHECK'))
		{
			$flags = JSON_NUMERIC_CHECK;
		}

        parent::__construct(
            json_encode($data, $flags),
            $status,
            array_merge(array('Content-Type' => 'application/json'), $headers)
        );
    }

    /**
     * {@inheritDoc}
     */
    static public function create($data = array(), $status = 200, $headers = array())
    {
        return new static($data, $status, $headers);
    }
}
