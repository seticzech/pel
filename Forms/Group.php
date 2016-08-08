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

use \Pel\Common\Data as CommonData;

/**
 * Class for manage groups of form elements
 *
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Group implements \Countable, \Iterator
{
	
	/**
	 * Group caption
	 * 
	 * @var string
	 */
	protected $_caption;
	
	/**
	 * Array of added element names
	 * 
	 * @var array
	 */
	protected $_elementNames = array();
	
	/**
	 * Parent form
	 * 
	 * @var \Phalcon\Forms\Form
	 */
	protected $_form;
	
	/**
	 * Group name
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Group options
	 * 
	 * @var array
	 */
	protected $_options;
	
	/**
	 * Internal counter
	 * 
	 * @var integer
	 */
	private $__position;
	
	/**
	 * Instances of sub-groups
	 * 
	 * @var array
	 */
	protected $_subGroups;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param \Phalcon\Forms\Form $form instance of the form
	 * @param string $caption (OPTIONAL) group caption
	 * @param array $elements (OPTIONAL) array of elements to add
	 * @throws \Phalcon\Forms\Exception
	 */
	public function __construct($name, $form, $caption = null, array $elements = array())
	{
		if (! $form instanceof \Phalcon\Forms\Form) {
			throw new \Phalcon\Forms\Exception("Form must be an instance of Phalcon\Forms\Form", 1);
		}
		
		$this->_caption = $caption;
		$this->_form = $form;
		$this->_name = $name;
		$this->_options = array();
		$this->__position = 0;
		$this->_subGroups = array();
		
		if (! empty($elements)) {
			foreach ($elements as $elm) {
				$this->add($elm);
			}
		}
	}
	
	/**
	 * Add element
	 * 
	 * @param string|\Phalcon\Forms\Element $element
	 * @param boolean $toForm if TRUE element will be added to the form automatically
	 * @throws \Phalcon\Forms\Exception
	 * @return \Pel\Forms\Group
	 */
	public function add($element, $toForm = true)
	{
		if (is_string($element)) {
			if (! $this->_form->has($element)) {
				throw new \Phalcon\Forms\Exception("Parent form does not contain element with specified name", 1);
			}
			
			$this->_elementNames[] = $element;
		} else if (! $element instanceof \Phalcon\Forms\Element) {
			throw new \Phalcon\Forms\Exception("Element must be an instance of Phalcon\Forms\Element", 1);
		} else {
			if ((true === $toForm) && ! $element->getForm()) {
				$this->_form->add($element);
			}
			$this->_elementNames[] = $element->getName();
		}
		
		return $this;
	}
	
	/**
	 * Create instance of the new group and add it as sub-group
	 * 
	 * @param string $name group name
	 * @param string $caption (OPTIONAL) group caption
	 * @param array $elements array of elements to add to sub-group
	 * @return \Pel\Forms\Group
	 */
	public function createSubGroup($name, $caption = null, array $elements = array())
	{
		$subGroup = new \Pel\Forms\Group($name, $this->_form, $caption, $elements);
		
		$this->_subGroups[$name] = $subGroup;
		
		if (! empty($elements)) {
			foreach ($elements as $element) {
				$subGroup->add($element);
			}
		}
		
		return $subGroup;
	}
	
	/**
	 * Returns count of the elements
	 * 
	 * @return integer
	 */
	public function count()
	{
		return count($this->_elementNames);
	}
	
	/**
	 * Returns current element by internal counter
	 * 
	 * @return \Phalcon\Forms\Element
	 */
	public function current()
	{
		$name = $this->_elementNames[$this->__position];
		return $this->get($name);
	}
	
	/**
	 * Get element
	 * 
	 * @param string $name element name
	 * @throws \Phalcon\Forms\Exception
	 * @return \Phalcon\Forms\Element
	 */
	public function get($name)
	{
		if (! in_array($name, $this->_elementNames)) {
			throw new \Phalcon\Forms\Exception("Group does not contain element '{$name}'", 1);
		}
		
		return $this->_form->get($name);
	}
	
	/**
	 * Return caption
	 * 
	 * @return string
	 */
	public function getCaption()
	{
		return $this->_caption;
	}
	
	/**
	 * Get all elements
	 * 
	 * @return array
	 */
	public function getElements()
	{
		$this->__reset();
		
		$result = array();
		foreach ($this->_elementNames as $name) {
			$element = $this->get($name);
			$result[$name] = $element;
		}
		
		return $result;
	}
	
	/**
	 * Get group name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Get names of the elements
	 * 
	 * @return array
	 */
	public function getNames()
	{
		$this->__reset();
		return $this->_elementNames;
	}
	
	/**
	 * Get option
	 * 
	 * @param string $name option name
	 * @param mixed $default (OPTIONAL) default value for non-existent option
	 * @return mixed
	 */
	public function getOption($name, $default = null)
	{
		return array_key_exists($name, $this->_options) ? $this->_options[$name] : $default;
	}
	
	/**
	 * Get all options
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
	
	/**
	 * Get specific sub-group by name
	 * 
	 * @param string $name
	 * @return \Pel\Forms\Group
	 */
	public function getSubGroup($name)
	{
		return isset($this->_subGroups[$name]) ? $this->_subGroups[$name] : null;
	}
	
	/**
	 * Get sub-groups
	 * 
	 * @return array
	 */
	public function getSubGroups()
	{
		return $this->_subGroups;
	}
	
	/**
	 * Get sub-groups names
	 * 
	 * @return array
	 */
	public function getSubGroupsNames()
	{
		return array_keys($this->_subGroups);
	}
	
	/**
	 * Get values of elements from group and its sub-groups (if any)
	 * 
	 * @return array
	 */
	public function getValues($options = array())
	{
		$defaults = array(
			"multiDim" => true,
			"ommitNulls" => true
		);
		$options = array_merge($defaults, $options);
		
		$result = array();
		
		foreach ($this->_elementNames as $name) {
			$element = $this->get($name);
			$key = $element->getName();
			$value = $element->getValue();
			
			if ((null === $value) && (boolean) $options["ommitNulls"]) {
				continue;
			}
				
			if ((boolean) $options["multiDim"]) {
				CommonData::arraySetMultiDimByFlat($result, $key, $value);
			} else {
				$result[$key] = $value;
			}
		}
		
		if (count($this->_subGroups) > 0) {
			foreach ($this->_subGroups as $subGroup) {
				$values = $subGroup->getValues($options);
				
				if (! empty($values)) {
					$result = array_merge_recursive($result, $values);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Check if group contains specific element
	 *  
	 * @param string $name element name
	 * @return boolean
	 */
	public function has($name)
	{
		return in_array($name, $this->_elementNames);
	}
	
	/**
	 * Check if elements in group have any messages
	 * 
	 * @return boolean
	 */
	public function hasMessages($includeSubGroups = true)
	{
		foreach ($this->_elementNames as $name) {
			if ($this->_form->hasMessagesFor($name)) {
				return true;
			}
		}
		
		if ($includeSubGroups && ! empty($this->_subGroups)) {
			foreach ($this->_subGroups as $subGroup) {
				if ($subGroup->hasMessages($includeSubGroups)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Check if sub-group of specific name exists
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function hasSubGroup($name)
	{
		return isset($this->_subGroups[$name]);
	}
	
	/**
	 * Check if exists any sub-groups
	 * 
	 * @return boolean
	 */
	public function hasSubGroups()
	{
		return count($this->_subGroups) > 0;
	}
	
	/**
	 * Get current internal counter
	 * 
	 * @return integer
	 */
	public function key()
	{
		return $this->__position;
	}
	
	/**
	 * Set the internal counter to the next element
	 * 
	 * @return void
	 */
	public function next()
	{
		$this->__position++;
	}
	
	/**
	 * Remove element
	 * 
	 * @param string $name element name
	 * @return boolean
	 */
	public function remove($name)
	{
		$key = array_search($name, $this->_elementNames);
		
		if (false !== $key) {
			unset($this->_elementNames[$key]);
		}
		
		return (false !== $key);
	}
	
	/**
	 * Reset array of elements
	 * 
	 * @param boolean $validate (OPTIONAL) validate elements againts form
	 * @return void
	 */
	private function __reset($validate = true)
	{
		if ($this->count() == 0) {
			$this->_elementNames = array();
			return;
		}
		
		if ($validate) {
			foreach ($this->_elementNames as $key => $e) {
				if (! $this->_form->has($e)) {
					unset($this->_elementNames[$key]);
				}
			}
		}
		
		$this->_elementNames = array_values($this->_elementNames);
	}
	
	/**
	 * Rewind the internal counter to the first position
	 * 
	 * @return void
	 */
	public function rewind()
	{
		$this->__position = 0;
	}
	
	/**
	 * Set group caption
	 * 
	 * @param string|null $value cpation value
	 * @throws Exception
	 * @return \Pel\Forms\Group
	 */
	public function setCaption($value)
	{
		if (! is_string($value) && (null !== $value)) {
			throw new Exception("Group caption can be a strings or NULL value", 1);
		}
		$this->_caption = $value;
		
		return $this;
	}
	
	/**
	 * Set option value
	 * 
	 * @param string $name option name
	 * @param mixed $value option value
	 * @return \Pel\Forms\Group
	 */
	public function setOption($name, $value)
	{
		if (! is_scalar($name)) {
			throw new \Pel\Forms\Exception("Option name must have a scalar value");
		}
		
		$this->_options[$name] = $value;
		return $this;
	}
	
	/**
	 * Set options 
	 * 
	 * @param array $options new options
	 * @return \Pel\Forms\Group
	 */
	public function setOptions(array $options)
	{
		$this->_options = $options;
		return $this;
	}
	
	/**
	 * Get count of sub-groups
	 * 
	 * @return integer
	 */
	public function subGroupsCount()
	{
		return count($this->_subGroups);
	}
	
	/**
	 * Check for valid internal counter value
	 * 
	 * @return boolean
	 */
	public function valid()
	{
		return isset($this->_elementNames[$this->__position]);
	}
	
}
