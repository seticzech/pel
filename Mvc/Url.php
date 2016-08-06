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

namespace Pel\Mvc;

/**
 * Class for manage with URLs
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Url extends \Phalcon\Mvc\Url
{

	/**
	 * Host (including port)
	 *
	 * @var string
	 */
	protected $_host;
	
	/**
	 * Scheme
	 *
	 * @var string
	 */
	protected $_scheme;
	
	/**
	 * Specific URL prefix
	 * 
	 * @var string
	 */
	protected $_urlPrefix;
	
	/**
	 * Exclusions of URLs to not to add URL prefix
	 * 
	 * @var array
	 */
	protected $_urlPrefixExclude;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_urlPrefixExclude = array();
		
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true)) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}
		$this->setScheme($scheme);
		
		if (isset($_SERVER['HTTP_HOST']) && ! empty($_SERVER['HTTP_HOST'])) {
			$this->setHost($_SERVER['HTTP_HOST']);
		} else if (isset($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'])) {
			$name = $_SERVER['SERVER_NAME'];
			$port = $_SERVER['SERVER_PORT'];
			
			if (($scheme == 'http' && $port == 80) || ($scheme == 'https' && $port == 443)) {
				$this->setHost($name);
			} else {
				$this->setHost($name . ':' . $port);
			}
		}		
	}
	
	public function get($uri = null, $args = null, $local = null, $baseUri = null)
	{
		if (! empty($this->_urlPrefix)) {
			$prefix = $this->_urlPrefix;
			
			$uriPart = "";
			if (! empty($uri)) {
				$parts = explode("/", $uri);
				$uriPart = $parts[0];
			}
			
			if (! in_array($uriPart, $this->_urlPrefixExclude)) {
				if (! empty($uri) && ($uri[0] == "/")) {
					$prefix = "/" . ltrim($prefix, "/");
				}
				
				$uri = $prefix . "/" . ltrim($uri, "/");
			}
		}
		
		return parent::get($uri, $args, $local, $baseUri);
	}
	
	/**
	 * Get full URL with all parts
	 * 
	 * @param string $uri
	 * @param string $args
	 * @param string $local
	 * @return string
	 */
	public function getFull($uri = null, $args = null, $local = null, $baseUri = null)
	{
		// fix Phalcon double slash error
		//$uri = ltrim($uri, "/");
		$url = $this->get($uri, $args, $local, $baseUri);
		
		if (false === strpos($url, "://")) {
			$url = $this->getScheme() . "://" . $this->getHost() . $url;
		}
		
		return $url;
	}
	
	public function getHost()
	{
		return $this->_host;
	}
	
	public function getScheme()
	{
		return $this->_scheme;
	}
	
	public function getServer()
	{
		return $this->getScheme() . "://" . $this->getHost();
	}
	
	public function removeScheme($uri)
	{
		if (false !== $pos = strpos($uri, "://")) {
			$uri = substr($uri, $pos + 3);
		}
		
		return $uri;
	}
	
	/**
	 * 
	 * @param unknown $host
	 * @return \Pel\Mvc\Url
	 */
	public function setHost($host)
	{
		$this->_host = $host;
		return $this;
	}
	
	public function setIncludeModuleName($value)
	{
		$this->_includeModuleName = (boolean) $value;
		return $this;
	}
	
	public function setIncludeModuleNameExceptions($value)
	{
		if (! is_array($value)) {
			$value = array($value);
		}
		
		$this->_includeModuleNameExceptions = $value;
		
		return $this;
	}
	
	public function setScheme($scheme)
	{
		$this->_scheme = $scheme;
		return $this;		
	}
	
	public function setUrlPrefix($value)
	{
		$this->_urlPrefix = trim($value, "/");
		return $this;
	}
	
	public function setUrlPrefixModuleName($excludeDefault = true, $router = null)
	{
		$di = $this->getDI();
		
		if (null !== $router) {
			if (is_string($router)) {
				$routerName = $router;
				
				if (! $di->has($routerName)) {
					throw new Exception("Module name cannot be set as URL prefix, service name '{$routerName}' not found in registered DI services");
				}
				
				$router = $di->getShared($router);
				
				if (! $router instanceof \Phalcon\Mvc\Router) {
					throw new Exception("Module name cannot be set as URL prefix, specified DI service '{$routerName}' is not instance of \Phalcon\Mvc\Router");
				}
			} elseif (! $router instanceof \Phalcon\Mvc\Router) {
				throw new Exception("Module name cannot be set as URL prefix, parameter router is not instance of \Phalcon\Mvc\Router");
			}
		} else {
			if (! $di->has("router")) {
				throw new Exception("Module name cannot be set as URL prefix, no router found");
			}
			
			$router = $di->getShared("router");
		}
		
		$defaults = $router->getDefaults();
		$moduleName = $router->getModuleName();
		
		if (! $excludeDefault) {
			$this->setUrlPrefix($moduleName);
		} elseif (isset($defaults["module"]) && ($moduleName != $defaults["module"])) {
			$this->setUrlPrefix($moduleName);
		}
	}
	
	public function setUrlPrefixExclude($value)
	{
		if (! is_array($value)) {
			$value = array($value);
		}
		
		$this->_urlPrefixExclude = $value;
		
		return $this;
	}
	
}
