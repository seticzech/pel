<?php

namespace Pel\Mvc;

/**
 * MVC model class
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Model extends \Phalcon\Mvc\Model
{
	
	/**
	 * Last result for bulk insert, create, update, save methods
	 * 
	 * @var boolen
	 */
	protected $_lastResult = false;
	
	/**
	 * Append validate messages from specified model
	 * 
	 * @param \Phalcon\Mvc\Model $model
	 * @return void
	 */
	public function appendMessagesFrom($model)
	{
		if (! $model instanceof \Phalcon\Mvc\Model) {
			throw new \Phalcon\Mvc\Model\Exception("Cannot append messages, model must be an instance of \Phalcon\Mvc\Model");
		}
		
		if (count($model->getMessages()) > 0) {
			foreach ($model->getMessages() as $message) {
				$this->appendMessage($message);
			}
		}
	}
	
	/**
	 * Perform bulk insert
	 * 
	 * Parameters should be an array:
	 * - columns: list of target columns for insertion
	 * - values: set of values to insert
	 * 
	 * @param array $parameters
	 * @throws \Phalcon\Mvc\Model\Exception
	 * @return boolean
	 */
	public function bulkInsert($parameters)
	{
		if (! is_array($parameters)) {
			throw new \Phalcon\Mvc\Model\Exception("Bulk insert parameters should be an array");
		}
		
		$columns = (isset($parameters["columns"])) ? $parameters["columns"] : array();
		$values = (isset($parameters["values"])) ? $parameters["values"] : array();
		
		$insert = new Model\BulkInsert($this, $columns, $values);
		
		$result = $insert->execute();
		$this->_lastResult = $result;
		
		return $result;
	}
	
	/**
	 * Create a new record
	 * 
	 * @param array $data
	 * @param array $whiteList
	 * @return \Pel\Mvc\Model
	 */
	public static function createRecord($data, $whiteList = null)
	{
		$class = get_called_class();
		$object = new $class();
		
		$object->create($data, $whiteList);
		
		return $object;
	}
	
	/**
	 * Get model columns
	 * 
	 * @return array
	 */
	public function getColumns()
	{
		return $this->getModelsMetaData()->getAttributes($this);
	}
	
	/**
	 * Create model fields with default values 
	 * 
	 * @return \Asl\Model\Data\Base
	 */
	public function getDefaults()
	{
		$data = $this->getModelsMetaData()->getDefaultValues($this);
		
		foreach ($data as $field => $value) {
			if (! isset($this->{$field}) || (null === $this->{$field})) {
				$this->{$field} = $value;
			}
		}
		
		return $this;
	}
	
	/**
	 * Returns full model name including namespace
	 *
	 * @return string
	 */
	public function getFullModelName($doubleBackslashes = false)
	{
		$name = get_class($this);
	
		if ($doubleBackslashes) {
			$name = str_replace("\\", "\\\\", $name);
		}
	
		return $name;
	}
	
	/**
	 * Get last result for bulk insert, create, update, save methods
	 * 
	 * @return boolean
	 */
	public function getLastResult()
	{
		return $this->_lastResult;
	}
	
	/**
	 * Override save() for set last result property
	 * 
	 * @see \Phalcon\Mvc\Model::save()
	 * @param array $data
	 * @param array $whiteList
	 * @return boolean
	 */
	public function save($data = null, $whiteList = null)
	{
		$this->_lastResult = parent::save($data, $whiteList);
		return $this->_lastResult;
	}
	
}
