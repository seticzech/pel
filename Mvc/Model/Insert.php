<?php

namespace Pel\Mvc\Model;

/**
 * Class for bulk inserts
 * 
 * @author Jiri Pazdernik <jiri@pazdernik.net>
 */
class Insert
{
	
	protected $_columns;
	
	/**
	 * 
	 * @var \Phalcon\Mvc\Model
	 */
	protected $_model;
	
	protected $_sql;
	
	protected $_values;
	
	public function __construct($model, $columns = array())
	{
		$this->setModel($model);
		$this->setColumns($columns);
	}
	
	public function addValues($values)
	{
		if (! is_array($values)) {
			$values = array($values);
		}
		
		$this->_values[] = $values;
		
		return $this;
	}
	
	public function clearValues()
	{
		$this->_values = array();
		return $this;
	}
	
	public function execute()
	{
		$sql = $this->generateSql();
		
		$result = $this->_model->getWriteConnection()->execute($sql);
		return $result;
	}
	
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
	
	public function getColumns()
	{
		return $this->_columns;
	}
	
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
	
	public function setModel($model)
	{
		if ((null !== $model) && ! $model instanceof \Phalcon\Mvc\Model) {
			throw new \Phalcon\Mvc\Model\Exception("Model must be an instance of \Phalcon\Mvc\Model");
		}
		
		$this->_model = $model;
		$this->_sql = null;
		
		return $this;
	}
	
}