<?php

namespace Pel\Forms;

class PostObject
{
	
	protected $_data;
	
	public function __construct($data = null)
	{
		$this->setData($data);
	}
	
	private function __getValue($name, $data)
	{
		//zd($name);
		//zd($data);
		if (false === strpos($name, "[")) {
			return (isset($data[$name])) ? $data[$name] : null;
		}
		
		$bPos = strpos($name, "[");
		$key = substr($name, 0, $bPos);
		
		if (! isset($data[$key])) {
			return null;
		}
		
		$subName = substr($name, $bPos + 1);
	
		$ePos = strpos($subName, "]");
		if (false === $ePos) {
			// no close ]
			return null;
		}
		$subName = substr_replace($subName, "", $ePos, 1);
		
		if (! empty($subName)) {
			return $this->__getValue($subName, $data[$key]);
		}
		
		return $data[$key];
	}
	
	public function readAttribute($name)
	{
		if (false !== strpos($name, "[]")) {
			throw new Exception("Undetermined arrays [] not supported yet", 1);
		}
		
		if (! is_array($this->_data)) {
			return null;
		}
		if (empty($this->_data)) {
			return null;
		} 
		if (false === strpos($name, "[")) {
			$r = (isset($this->_data[$name])) ? $this->_data[$name] : null;
			return $r;
		}
		
		$r = $this->__getValue($name, $this->_data);
		return $r;
	}
	
	public function setData($data)
	{
		$this->_data = $data;
	}
	
}
