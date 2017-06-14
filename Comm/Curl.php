<?php

namespace Pel\Comm;

class Curl
{
	
	// request methods
	const REQUEST_METHOD_GET = "GET";
	const REQUEST_METHOD_POST = "POST";
	const REQUEST_METHOD_PUT = "PUT";
	
	protected $_errorNo = 0;
	
	protected $_errorMsg = null;
	
	protected $_headers = array();
	
	protected $_info = null;
	
	protected $_response = null;
	
	public function __construct()
	{
		
	}
	
	public function addHeader($value)
	{
		$this->_headers[] = $value;
	}
	
	/*public function __getUrl($url, $params = null)
	{
		if (null === $params) {
			return $url;
		}
		
		if (is_array($params)) {
			$parts = array();
			foreach ($params as $key => $val) {
				$val = urlencode($val);
				
			}
		}
	}*/
	
	public function getLastError()
	{
		return $this->_errorNo;
	}
	
	public function getLastErrorMessage()
	{
		return $this->_errorMsg;
	}
	
	public function getLastInfo()
	{
		return $this->_info;
	}
	
	public function getLastResponse()
	{
		return $this->_response;
	}
	
	public function get($url)
	{
		return $this->send($url, null, self::REQUEST_METHOD_GET);
	}
	
	public function post($url, $data = null)
	{
		return $this->send($url, $data);
	}
	
	public function put($url, $data = null)
	{
		return $this->send($url, $data, self::REQUEST_METHOD_PUT);
	}
	
	public function send($url, $data = null, $method = self::REQUEST_METHOD_POST)
	{
		$curl = curl_init();
		
		if (null !== $data) {
			if (($method === self::REQUEST_METHOD_POST) || ($method === self::REQUEST_METHOD_PUT)) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			/*} else {
				if (is_array($data)) {
					$data = 
				}*/
			}
			//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		
		curl_setopt($curl, CURLOPT_COOKIESESSION, true);
		//curl_setopt($curl, CURLOPT_HEADER, true); // headers in response
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$this->_response = curl_exec($curl);
		$this->_errorNo = curl_errno($curl);
		$this->_errorMsg = curl_error($curl);
		$this->_info = curl_getinfo($curl);
		
		//zd($this->_errorNo);
		//zd($this->_errorMsg);
		//zd($this->_info);
		
		curl_close($curl);
		
		return $this->_response;
	}
	
}
