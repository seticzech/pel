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
 * Class extending default PHP ArrayObject class
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class ArrayObject extends \ArrayObject
{
	
	/**
	 * Constructor
	 * 
	 * @see \ArrayObject class
	 * @param array $input
	 * @param integer $flags
	 * @param string $iteratorClass
	 */
	public function __construct($input = array(), $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = "ArrayIterator")
	{
		$items = array();
		
		foreach ($input as $key => $item) {
			if (is_array($item)) {
				$item = new ArrayObject($item, $flags, $iteratorClass);
			}
			$items[$key] = $item;
		}
		
		parent::__construct($items, $flags, $iteratorClass);
	}
	
	/**
	 * Return data as an array
	 * 
	 * @param boolean $recursive TRUE for convert children to array
	 * @return array
	 */
	public function toArray($recursive = false)
	{
		$result = array();
		
		foreach ($this as $key => $val) {
			if ((true ===  $recursive) && is_object($val) && method_exists($val, "toArray")) {
				$val = $val->toArray();
			}
			
			$result[$key] = $val;
		}
		
		return $result;
	}
	
}
