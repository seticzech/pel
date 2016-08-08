<?php

namespace Pel\Mvc\Model;

use \Phalcon\Mvc\Model\Message as Message;

/**
 * Class for bulk inserts
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class BulkInsert
{
	
	/**
	 * List of target columns for insertion
	 * 
	 * @var array
	 */
	protected $_columns;
	
	/**
	 * Target model
	 * 
	 * @var \Phalcon\Mvc\Model
	 */
	protected $_model;
	
	/**
	 * Generated SQL
	 * 
	 * @var string
	 */
	protected $_sql;
	
	/**
	 * Values to insert
	 * 
	 * @var array
	 */
	protected $_values;
	
	/**
	 * Constructor
	 * 
	 * @param \Phalcon\Mvc\Model $model target model
	 * @param array $columns (OPTIONAL) list of target columns
	 * @param array $values (OPTIONAL) values to insert
	 */
	public function __construct($model, $columns = array(), $values = array())
	{
		$this->setModel($model);
		$this->setColumns($columns);
		$this->setValues($values);
	}
	
	/**
	 * Add values to insert
	 * 
	 * @param array $values
	 * @return \Pel\Mvc\Model\BulkInsert
	 */
	public function addValues($values)
	{
		if (! is_array($values)) {
			$values = array($values);
		}
		
		$this->_values[] = $values;
		
		return $this;
	}
	
	/**
	 * Clear values to insert
	 * 
	 * @return \Pel\Mvc\Model\BulkInsert
	 */
	public function clearValues()
	{
		$this->_values = array();
		return $this;
	}
	
	/**
	 * Execute insert
	 * 
	 * @return boolean
	 */
	public function execute()
	{
		$sql = $this->generateSql();
		
		try {
			$result = $this->_model->getWriteConnection()->execute($sql);
		} catch (\Exception $e) {
			$message = new Message($e->getMessage());
			$this->_model->appendMessage($message);
			$result = false;
		}
		
		return $result;
	}
	
	/**
	 * Generate SQL
	 * 
	 * @throws \Phalcon\Mvc\Model\Exception
	 * @return string
	 */
	public function generateSql()
	{
		if (null !== $this->_sql) {
			return $this->_sql;
		}
		
		if (null === $this->_model) {
			throw new \Phalcon\Mvc\Model\Exception("SQL cannot be generated, unknown model");
		}
		if (empty($this->_columns)) {
			throw new \Phalcon\Mvc\Model\Exception("SQL cannot be generated, columns are empty");
		}
		if (empty($this->_values)) {
			throw new \Phalcon\Mvc\Model\Exception("SQL cannot be generated, values are empty");
		}
		
		$db = $this->_model->getWriteConnection();
		
		$columns = array();
		foreach ($this->_columns as $col) {
			$col = $db->escapeIdentifier($col);
			$columns[] = $col;
		}
		$columns = implode(",", $columns);
		
		$colCount = count($this->_columns);
		$rows = array();
		foreach ($this->_values as $index => $row) {
			if (count($row) !== $colCount) {
				throw new \Phalcon\Mvc\Model\Exception("Values in row at index {$index} does not match columns count");
			}
			
			$values = array();
			foreach ($row as $val) {
				if (! is_scalar($val) && (null !== $val)) {
					$valType = gettype($val);
					throw new \Phalcon\Mvc\Model\Exception("Only scalar values can be used for insert, row at index {$index} contains value of type '{$valType}'");
				}
				if (is_string($val)) {
					$val = $db->escapeString($val);
				} elseif (null === $val) {
					$val = "NULL";
				}
				$values[] = $val;
			}
			$values = "(" . implode(",", $values) . ")";
			
			$rows[] = $values;
		}
		$rows = implode(",", $rows);
		
		$tableName = $db->escapeIdentifier($this->_model->getSource());
		$sql = "INSERT INTO {$tableName} ({$columns}) VALUES {$rows}";
		
		$this->_sql = $sql;
		
		return $sql;
	}
	
	/**
	 * Get list of columns
	 * 
	 * @return array
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
	/**
	 * Set target columns for insertion
	 * 
	 * If $columns is empty all columns of model will be set
	 * 
	 * @param array $columns (OPTIONAL) list of columns
	 * @return \Pel\Mvc\Model\BulkInsert
	 */
	public function setColumns($columns)
	{
		if (empty($columns)) {
			$columns = $this->_model->getColumns();
		}
		if (! is_array($columns)) {
			$columns = array($columns);
		}
		
		$this->_columns = $columns;
		$this->_sql = null;
		
		return $this;
	}
	
	/**
	 * Set target model
	 * 
	 * @param \Phalcon\Mvc\Model $model
	 * @throws \Phalcon\Mvc\Model\Exception
	 * @return \Pel\Mvc\Model\BulkInsert
	 */
	public function setModel($model)
	{
		if ((null !== $model) && ! $model instanceof \Phalcon\Mvc\Model) {
			throw new \Phalcon\Mvc\Model\Exception("Model must be an instance of \Phalcon\Mvc\Model");
		}
		
		$this->_model = $model;
		$this->_sql = null;
		
		return $this;
	}
	
	/**
	 * Set values to insert
	 * 
	 * @param array $values
	 * @return \Pel\Mvc\Model\BulkInsert
	 */
	public function setValues($values)
	{
		$this->_values = $values;
		$this->_sql = null;
		
		return $this;
	}
	
}