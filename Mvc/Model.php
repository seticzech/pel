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
	 * Last result for bulk insert, create, update, save, executeRawSql methods
	 * 
	 * @var boolean
	 */
	protected $_lastResult = null;
	
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
	 * - columns: list of target columns (could be empty for all columns in table)
	 * - values: set of values to insert into specified columns
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
	 * Create new instance and fill last result
	 * 
	 * @param mixed $data 
	 * @param mixed $whiteList 
	 * @return boolean
	 */
	public function create($data = null, $whiteList = null)
	{
		$this->_lastResult = parent::create($data, $whiteList);
		return $this->_lastResult;
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
	 * Execute prepared raw SQL
	 * 
	 * @param string $sql
	 * @return boolean
	 */
	public function executeRawSql($sql)
	{
		$db = $this->getWriteConnection();
		$this->_lastResult = $db->execute($sql);
		
		if (false === $this->_lastResult) {
			foreach ($db->getErrorInfo() as $message) {
				$this->appendMessage(new \Phalcon\Mvc\Model\Message($message));
			}
		}
		
		return $this->_lastResult;
	}
	
	/**
	 * Get model columns
	 * 
	 * @param string $alias (OPTIONAL) prepend an alias before columns names
	 * @param array $addColumns (OPTIONAL) append another columns after model columns
	 * @return array
	 */
	public function getColumns($alias = null, $addColumns = array())
	{
		$columns = $this->getModelsMetaData()->getAttributes($this);
		
		// rename real columns to the mapped if exists
		$map = $this->getModelsMetaData()->getColumnMap($this);
		if (! empty($map)) {
			foreach ($columns as $key => $val) {
				if (array_key_exists($val, $map)) {
					$columns[$key] = $map[$val];
				}
			}
		}
		
		if (! empty($alias)) {
			foreach ($columns as & $val) {
				$val = $alias . "." . $val;
			}
		}
		
		if (! empty($addColumns)) {
			$columns = array_merge($columns, $addColumns);
		}
		
		return $columns;
	}
	
	/**
	 * Get all columns as list for mapping
	 * 
	 * Method returns all columns as array where
	 * each key equals its value. In table with three
	 * columns: "id", "date", "name" the method returns
	 * array containing these values:
	 * ["id" => "id", "date" => "date", "name" => "name".
	 * So columns can be renamed easily to the new values.
	 * 
	 * @return array
	 */
	public function getAllColumnsForMapping()
	{
		$columns = $this->getModelsMetaData()->getAttributes($this);
		$result = array();
		
		foreach ($columns as $col) {
			$result[$col] = $col;
		}
		
		return $result;
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
	
	public function getMessagesAsString($separator = "\n", $filter = null)
	{
		$result = array();
		
		if (count($this->getMessages($filter)) > 0) {
			foreach ($this->getMessages($filter) as $msg) {
				$result[] = $msg;
			}
		}
		
		$result = implode($separator, $result);
		
		return $result;
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
