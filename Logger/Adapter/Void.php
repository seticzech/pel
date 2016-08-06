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

namespace Pel\Logger\Adapter;

class Void extends \Phalcon\Logger\Adapter implements \Phalcon\Logger\AdapterInterface
{
	
	public function close()
	{
		
	}
	
	public function getFormatter()
	{
		return $this->_formatter;
	}
	
	public function logInternal($message, $type, $time, $context)
	{
		
	}
	
}
