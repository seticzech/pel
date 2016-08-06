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
 * Class for debug appliaction and symbols
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Debug
{
	
	/**
	 * Dump specific variable
	 * 
	 * @param mixed $var variable to dump
	 * @param string $label (OPTIONAL) text of user defined label
	 * @param boolean $echo (OPTIONAL) FALSE for suppress print of the dump
	 * @param boolean $wrapPre (OPTIONAL) FALSE for not to wrap content with <pre> tag
	 * @return string information about dumped variable
	 */
	public static function dump($var, $label = null, $echo = true, $wrapPre = true)
	{
		ob_start();
		var_dump($var);
		$r = ob_get_clean();
		
		$result = $label . " " . $r;
		if ($wrapPre) {
			$result = "<pre>" . $result . "</pre>";
		}
		
		if ($echo) {
			echo $result;
		}
		
		return $result;
	}
	
}