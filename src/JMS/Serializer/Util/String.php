<?php

namespace JMS\Serializer\Util;

/**
 * Class String
 * @package JMS\Serializer\Util
 * @author  Bubelbub <bubelbub@gmail.com>
 */
class String
{
	/**
	 * @var string
	 */
	private $content = null;

	/**
	 * @param string $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->content;
	}
}
