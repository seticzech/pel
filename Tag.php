<?php
/**
 * --------------------------------
 * PEL - Phalcon Extensions Library
 * --------------------------------
 *
 * This code is distributed under New BSD license.
 * License is bundled with this package in file LICENSE.txt.
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */

namespace Pel;

/**
 * Extended Tag class
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Tag extends \Phalcon\Tag
{
	
	/**
	 * Builds a HTML button[type=button] tag
	 * 
	 * @param array $parameters
	 * @return string
	 */
	static function button($parameters = null)
	{
		return self::_buttonField("button", $parameters);
	}
	
	/**
	 * Builds a HTML button[type=submit] tag
	 * 
	 * @param array $parameters
	 * @return string
	 */
	static function submitButton($parameters = null)
	{
		return self::_buttonField("submit", $parameters);
	}
	
	/**
	 * Builds generic BUTTON tags
	 * 
	 * @param string $type
	 * @param array $parameters
	 * @return string
	 */
	static protected final function _buttonField($type, $parameters)
	{
		if (! is_array($parameters)) {
			$parameters = array($parameters);
		}
		
		if (array_key_exists(0, $parameters)) {
			$parameters["id"] = $parameters[0];
		}
		if (! isset($parameters["name"])) {
			$parameters["name"] = $parameters["id"];
		}
		if (! empty($type)) {
			$parameters["type"] = $type;
		}
		
		$content = (array_key_exists("value", $parameters))
			? $parameters["value"]
			: null;
		
		$xhtml = self::renderAttributes("<button", $parameters) . ">{$content}</button>";
		
		return $xhtml;
	}
	
}
