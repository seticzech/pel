<?php

namespace Pel\Common;

class Data
{
	
	/**
	 * Set value by flat key name in multi-dim array
	 * 
	 * @param array $data array to set
	 * @param string $key key name in flat format like "a[b][c]"
	 * @param mixed $value value to set
	 * @return void
	 */
	public static function arraySetMultiDimByFlat(& $data, $key, $value)
	{
		if (empty($key)) {
			$data = $value;
			return;
		}
		
		$lPos = strpos($key, "[");
		
		if (false === $lPos) {
			$data[$key] = $value;
			return;
		}
		
		$rPos = strpos($key, "]", $lPos);
		
		if (false === $rPos) {
			$data[$key] = $value;
			return;
		}
		
		$subKey = substr($key, $lPos + 1);
		$subKey = preg_replace("/]/", "", $subKey, 1);
		$key = substr($key, 0, $lPos);
		
		if (empty($data[$key])) {
			$data[$key] = array();
		}
		
		self::arraySetMultiDimByFlat($data[$key], $subKey, $value);
	}
	
	/**
	 * Return value from multi-dim array by flat key
	 * 
	 * @param array $source source array
	 * @param string $key key name in flat format like "a[b][c]"
	 * @return mixed
	 */
	public static function arrayGetMultiDimByFlat($source, $key)
	{
		$lPos = strpos($key, "[");
		
		if (false === $lPos) {
			return (isset($source[$key])) ? $source[$key] : null;
		}
		
		$rPos = strpos($key, "]", $lPos);
		
		if (false === $rPos) {
			return null;
		}
		
		$subKey = substr($key, $lPos + 1);
		$subKey = preg_replace("/]/", "", $subKey, 1);
		$key = substr($key, 0, $lPos);
		
		if (isset($source[$key])) {
			return self::arrayGetMultiDimByFlat($source[$key], $subKey);
		}
		
		return null;
	}
	
}
