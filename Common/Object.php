<?php

namespace Pel\Common;

class Object
{
	
	/**
	 * Cast instance to another class
	 * 
	 * @param object $instance
	 * @param string $className
	 * @return mixed
	 */
	public static function cast($instance, $className)
	{
		return unserialize(sprintf(
			'O:%d:"%s"%s',
			strlen($className),
			$className,
			strstr(strstr(serialize($instance), '"'), ':')
		));		
	}
	
}
