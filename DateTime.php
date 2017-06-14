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
 * Class extending default PHP DateTime class
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class DateTime extends \DateTime
{
	
	/**
	 * Default date format
	 * 
	 * @var string
	 */
	const DEFAULT_DATE_FORMAT = "Y-m-d";
	
	/**
	 * Default time format
	 * 
	 * @var string
	 */
	const DEFAULT_TIME_FORMAT = "H:i:s";
	
	/**
	 * Default format for self::format() method
	 * 
	 * @var string
	 */
	protected $_defaultFormat = self::ATOM;
	
	/**
	 * Add days to date
	 * 
	 * @param number $days number of days to add
	 * @return \Pel\DateTime
	 */
	public function addDay($days = 1)
	{
		$days = (int) $days;
		$this->add(new \DateInterval("P{$days}D"));
		
		return $this;
	}
	
	/**
	 * Returns new DateTime object formatted according to the specified format
	 * 
	 * @param string $format format string
	 * @param string $time date and time value
	 * @param object $timezone
	 * @return \Pel\DateTime
	 */
	public static function createFromFormat($format, $time, $timezone = null)
	{
		$dt = new static();
		
		if (null === $timezone) {
			$parent = parent::createFromFormat($format, $time);
		} else {
			$parent = parent::createFromFormat($format, $time, $timezone);
		}
		if (false === $parent) {
			return false;
		}
		
		$dt->setDefaultFormat($format);
		$dt->setTimestamp($parent->getTimestamp());
		return $dt;
	}
	
	/**
	 * Returns date formatted according to given format
	 * 
	 * @param string $format (OPTIONAL) format parameters
	 * @return string
	 */
	public function format($format = null)
	{
		if (null === $format) {
			$format = $this->_defaultFormat;
		}
	
		return parent::format($format);
	}
	
	/**
	 * Returns date part formatted according to given format
	 * 
	 * @param string $format (OPTIONAL) format parameters
	 * @return string
	 */
	public function getDate($format = null)
	{
		if (null === $format) {
			$format = self::DEFAULT_DATE_FORMAT;
		}
	
		return $this->format($format);
	}
	
	/**
	 * Get default format
	 * 
	 * @return string
	 */
	public function getDefaultFormat()
	{
		return $this->_defaultFormat;
	}
	
	/**
	 * Returns time part formatted according to given format
	 * 
	 * @param string $format (OPTIONAL) format parameters
	 * @return string
	 */
	public function getTime($format = null)
	{
		if (null === $format) {
			$format = self::DEFAULT_TIME_FORMAT;
		}
	
		return $this->format($format);
	}
	
	/**
	 * Check if date is weekend
	 * 
	 * @return boolean
	 */
	public function isWeekend()
	{
		return $this->format("N") > 5;
	}
	
	/**
	 * Set new date and/or time formatted according to the specified format
	 * 
	 * @param string $format format string
	 * @param string $time date and time value
	 * @return \Pel\DateTime
	 */
	public function setFromFormat($format, $time)
	{
		$parent = parent::createFromFormat($format, $time);
		
		if (false !== $parent) {
			$this->setTimestamp($parent->getTimestamp());
		}
		
		return $dt;
	}
	
	/**
	 * Set new date and/or time formatted according to the default format
	 * 
	 * @param string $time date and time value
	 * @return \Pel\DateTime
	 */
	public function set($time)
	{
		$format = $this->_defaultFormat;
		$parent = parent::createFromFormat($format, $time);
		
		if (false !== $parent) {
			$this->setTimestamp($parent->getTimestamp());
		}
		
		return $this;
	}
	
	/**
	 * Set default format
	 * 
	 * @param string $format
	 * @return \Pel\DateTime
	 */
	public function setDefaultFormat($format)
	{
		$this->_defaultFormat = $format;
		return $this;
	}
	
}