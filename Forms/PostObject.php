<?php

namespace Pel\Forms;

/**
 * Class PostObject for validate multi-dimensional forms
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class PostObject
{
	
	/**
	 * POST data
	 * 
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Contructor
	 * 
	 * @param array $data POST data
	 */
	public function __construct($data = null)
	{
		$this->setData($data);
	}
	
	/**
	 * Get proper value for element
	 * 
	 * @param string $name element name
	 * @param array $data POST data
	 * @return mixed
	 */
	private function __getValue($name, $data)
	{
		//zdToFile($name);
		//zdToFile($data);
		
		return \Pel\Common\Data::arrayGetMultiDimByFlat($data, $name);
	}
	
	/**
	 * Read attribute value
	 * 
	 * @param string $name attribute name
	 * @throws Exception
	 * @return mixed
	 */
	public function readAttribute($name)
	{
		if (false !== strpos($name, "[]")) {
			throw new \Phalcon\Forms\Exception("Undetermined arrays [] not supported yet", 1);
		}
		
		if (! is_array($this->_data)) {
			return null;
		}
		if (empty($this->_data)) {
			return null;
		} 
		
		$r = $this->__getValue($name, $this->_data);
		return $r;
	}
	
	/**
	 * Set POST data
	 * 
	 * @param array $data POST data
	 * @return void
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}
	
}
