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

class Logger
{
	
	/**
	 * Logger status
	 * 
	 * @var boolean
	 */
	private $__enabled;
	
	/**
	 * Array of logger adapters
	 * 
	 * @var array
	 */
	private $__loggers = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->create("void");
		$this->setEnabled(true);
	}
	
	/**
	 * Returns specified previously created logger adapter
	 * 
	 * @param string $name name of logger
	 * @throws \Phalcon\Logger\Exception
	 * @return \Pel\Logger\Adapter
	 */
	public function __get($name)
	{
		if (! isset($this->__loggers[$name])) {
			throw new \Phalcon\Logger\Exception("Logger with name '{$name}' doesn't exists");
		}
		
		return ($this->__enabled) ? $this->__loggers[$name] : $this->__loggers["void"];
	}
	
	/**
	 * Create logger adapter
	 * 
	 * @param string $loggerName name of the adapter
	 * @param array $config (OPTIONAL) configuration
	 * @return \Pel\Logger\Adapter
	 */
	public function create($loggerName, $config = array())
	{
		$adapter = self::factory($loggerName, $config);
		$this->__loggers[$loggerName] = $adapter;
		
		return $adapter;
	}
	
	/**
	 * Factory for creating logger adaters
	 * 
	 * @param string $loggerName name of the adapter
	 * @param array $config (OPTIONAL) configuration
	 * @throws \Phalcon\Logger\Exception
	 * @return \Pel\Logger\Adapter
	 */
	public static function factory($loggerName, $config = array())
	{
		if ($config instanceof \Phalcon\Config) {
			$config = $config->toArray();
		}
		
		if (isset($config["enabled"]) && (boolean) $config["enabled"]) {
			$type = isset($config["adapter"]) ? ucfirst(strtolower($config["adapter"])) : "File";
			$className = "\Pel\Logger\Adapter\\" . $type;

			if (! class_exists($className)) {
				throw new \Phalcon\Logger\Exception("Logger adapter: '{$type}' doesn't exists");
			}
			
			switch ($type) {
				case "File":
				case "Stream":
					$name = isset($config["name"]) ? $config["name"] : null;
					if (null === $name) {
						throw new \Phalcon\Logger\Exception("Logger adapter '{$type}' requests parameter 'name'");
					}
					if (("File" == $type) && (false === file_put_contents($name, "", FILE_APPEND))) {
						throw new \Phalcon\Logger\Exception("File: '{$name}' cannot be created or is not writable, logger cannot be created");
					}
					$adapter = new $className($name);
					break;
				default:
					$adapter = new $className();
					break;
			}
			
		} else {
			$adapter = new Logger\Adapter\Void();
		}
		
		if (isset($config["level"])) {
			$adapter->setLogLevel((int) $config["level"]);
		}
		
		return $adapter;
	}
	
	/**
	 * Set logger status
	 * 
	 * @param boolean $value status
	 * @return \Pel\Logger
	 */
	public function setEnabled($value)
	{
		$this->__enabled = (boolean) $value;
		
		return $this;
	}
	
}
