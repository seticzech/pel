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

namespace Pel\Forms;

/**
 * Class for working with forms
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Form extends \Phalcon\Forms\Form
{
	
	/**
	 * Groups of elements
	 * 
	 * @var array
	 */
	protected $_groups = array();
	
	/**
	 * JavaScript lines
	 * 
	 * @var array
	 */
	protected $_javaScript = array();
	
	/**
	 * Constructor
	 * 
	 * @param object $entity
	 * @param array $userOptions
	 */
	public function __construct($entity = null, $userOptions = null)
	{
		parent::__construct($entity, $userOptions);
		
		$this->initialize();
		
		$validation = new \Phalcon\Validation();
		$this->setValidation($validation);
	}
	
	/**
	 * Add group
	 * 
	 * @param string $name group name
	 * @param string $caption (OPTIONAL) group caption
	 * @param array $elements (OPTIONAL) elements to add
	 * @return \Pel\Forms\Group
	 */
	public function createGroup($name, $caption = null, array $elements = array())
	{
		if (isset($this->_groups[$name])) {
			throw new Exception("Error when creating form group. Group '{$name}' already exists in form", $code, $previous);
		}
		
		$group = new Group($name, $this, $caption, $elements);
		$this->_groups[$name] = $group;
		
		return $group;
	}
	
	public function addJS($line)
	{
		$this->_javaScript[] = $line;
		
		return $this;
	}
	
	/**
	 * Replace bind() for use with multidim arrays
	 * 
	 * @see \Phalcon\Forms\Form::bind()
	 */
	public function bind(array $data, $entity, array $whitelist = null)
	{
		parent::bind($data, $entity, $whitelist);
		
		foreach ($data as $key => $value) {
			if (! empty($whitelist) && ! isset($whitelist[$key])) {
				continue;
			}
			
			$this->_setDefault($key, $value);
		}
	}
	
	/**
	 * Get group
	 * 
	 * @param string $name group name
	 * @return \Pel\Forms\Group|null
	 */
	public function getGroup($name)
	{
		return isset($this->_groups[$name]) ? $this->_groups[$name] : null;
	}
	
	/**
	 * Get all groups in the form
	 * 
	 * @return array
	 */
	public function getGroups()
	{
		return $this->_groups;
	}
	
	/**
	 * Get all groups names
	 * 
	 * @return array
	 */
	public function getGroupsNames()
	{
		return array_keys($this->_groups);
	}
	
	/**
	 * Get all values
	 * 
	 * @param array $groups (OPTIONAL) get values only for specified groups
	 * @return array
	 */
	public function getValues($groups = array())
	{
		$result = array();
		
		if (! empty($groups)) {
			foreach ($groups as $name) {
				$result = array_merge($result, $this->getGroup($name)->getValues());
			}
		} else {
			foreach ($this as $element) {
				$result[$element->getName()] = $element->getValue();
			}
		}
		
		return $result;
	}
	
	/**
	 * Check if form contains specified group
	 * 
	 * @param string $name group name
	 * @return boolean
	 */
	public function hasGroup($name)
	{
		return isset($this->_groups[$name]);
	}
	
	/**
	 * Initialization
	 */
	public function initialize()
	{
		
	}
	
	/**
	 * Remove group
	 * 
	 * @param string $name group name
	 * @throws \Phalcon\Forms\Exception
	 * @return \Pel\Forms\Form
	 */
	public function removeGroup($name)
	{
		if (! array_key_exists($name, $this->_groups)) {
			throw new \Phalcon\Forms\Exception("Form does not contain group '{$name}'", 1);
		}
		
		unset($this->_groups[$name]);
		
		return $this;
	}
	
	/**
	 * Remove element from the form
	 * 
	 * @param string $name element name
	 * @return boolean
	 */
	public function remove($name)
	{
		if (! empty($this->_groups)) {
			foreach ($this->_groups as $group) {
				$group->remove($name);
			}
		}
		
		return parent::remove($name);
	}
	
	public function renderJS($lineBreaks = true)
	{
		$xhtml = "";
		$break = ($lineBreaks) ? "\n" : "";
		
		if (! empty($this->_javaScript)) {
			$xhtml .= "<script type=\"text/javascript\">" . $break;
			$xhtml .= implode($break, $this->_javaScript) . $break;
			$xhtml .= "</script>" . $break;
		}
		
		return $xhtml;
		
	}
	
	protected function _setDefault($key, $value)
	{
		if (is_array($value)) {
			foreach ($value as $subKey => $subVal) {
				$this->_setDefault($key . "[$subKey]", $subVal);
			}
		} else {
			if ($this->has($key)) {
				$this->get($key)->setDefault($value);
			}
		}
	}
	
	public function setDefaults($values)
	{
		foreach ($values as $key => $value) {
			$this->_setDefault($key, $value);
		}
	}
	
}
