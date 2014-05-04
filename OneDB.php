<?php

/**
 * OneDB Database Framework
 *
 * Based on the DB class from SlimFit
 * https://github.com/cvgellhorn/SlimFit/blob/master/lib/SF/Db.php
 *
 * @author cvgellhorn
 */
class OneDB
{
	/**
	 * Instance implementation
	 *
	 * @var OneDB
	 */
	private static $_instance = null;

	/**
	 * Collection of active database connections
	 *
	 * @var array of OneDB connections
	 */
	private static $_connections = array();

	/**
	 * PDO object
	 *
	 * @var PDO
	 */
	protected $_pdo;

	/**
	 * The driver level statement PDO
	 *
	 * @var PDOStatement
	 */
	protected $_stmt;

	/**
	 * Default DB configuration
	 *
	 * @var array
	 */
	protected $_config = array(
		'pdo_type'  => 'mysql',
		'host'      => 'localhost',
		'charset'   => 'utf-8',
		'database'  => null,
		'user'      => null,
		'password'  => null
	);

	/**
	 * Single pattern implementation
	 *
	 * @param array $config Connection configs
	 * @return OneDB
	 */
	public static function getInstance($config = array())
	{
		if (null === self::$_instance) {
			self::$_instance = self::_create($config);
		}
		return self::$_instance;
	}

	/**
	 * Handle database connections
	 *
	 * @param string $name Connection name
	 * @param array $config Connection configs
	 * @return OneDB
	 */
	public static function getConnection($name = null, $config = array())
	{
		if (null === $name) {
			return self::_create($config);
		} else {
			if (!isset(self::$_connections[$name])) {
				self::$_connections[$name] = self::_create($config);
			}
			return self::$_connections[$name];
		}
	}

	/**
	 * Create new OneDB connection
	 *
	 * @param array $config Connection configs
	 * @return OneDB
	 * @throws Exception
	 */
	private static function _create($config)
	{
		if (!empty($config)) {
			return new self($config);
		} else {
			throw new Exception('OneDB configuration not set');
		}
	}

	/**
	 * Create DB object and connect to database
	 */
	private function __construct($config)
	{
		try {
			if (!extension_loaded('pdo_mysql')) {
				throw new Exception('pdo_mysql extension is not installed');
			}

			// Prepare database configuration
			$config = $this->_prepareConfig($config);

			$dsn = array(
				'host='     . $config['host'],
				'dbname='   . $config['database'],
				'charset='  . $config['charset']
			);

			$this->_pdo = new PDO(
				$config['pdo_type'] . ':' . implode(';', $dsn),
				$config['user'],
				$config['password']
			);

			// Always use exceptions
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Set character encoding
			$this->_pdo->exec("SET CHARACTER SET utf8");
		} catch (PDOException $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Prepare database configuration
	 *
	 * @param array $config Connection configs
	 * @return array Matched connection configs
	 * @throws Exception
	 */
	private function _prepareConfig($config)
	{
		$config = array_merge($this->_config, $config);
		foreach ($config as $key => $val) {
			if (null === $val) {
				throw new Exception('Could not connect to database, missing parameter: ' . $key);
			}
		}

		return $config;
	}

	/**
	 * Prepare SQL statement for executing
	 *
	 * @param string $sql SQL statement
	 * @return OneDB
	 * @throws Exception
	 */
	private function _prepare($sql)
	{
		$this->_stmt = $this->_pdo->prepare($sql);
		return $this;
	}

	/**
	 * Build where clause
	 *
	 * @param array $where Where conditions
	 * @param string $query Query string
	 */
	private function _buildWhere(&$where, &$query)
	{
		if (!empty($where)) {
			$expr = array();
			foreach ($where as $key => $val) {
				if ($val instanceof OneExpr) {
					$expr[] = str_replace('?', $val, $key);
					unset($where[$key]);
				}
			}
			$query .= ' WHERE ' . implode(' AND ', array_keys($where))
				. (!empty($expr)) ? ' AND ' . implode(' AND ', $expr) : '';
		}
	}

	/**
	 * Bind SQL query params to PDO statement object
	 *
	 * @param array $data SQL query params
	 * @return OneDB
	 */
	private function _bindParams($data)
	{
		$count = count($data);
		for ($i = 0; $i < $count; $i++) {
			$this->_stmt->bindParam($i + 1, $data[$i]);
		}

		return $this;
	}

	/**
	 * Execute SQL statement
	 *
	 * @return PDOStatement
	 * @throws Exception
	 */
	private function _execute()
	{
		try {
			$this->_stmt->execute();
		} catch (PDOException $e) {
			throw new Exception('PDO Mysql execution error: ' . $e->getMessage(), $e->getCode());
		}

		return $this->_stmt;
	}

	/**
	 * Get the current PDO object
	 *
	 * @return PDO
	 */
	public function getPDO()
	{
		return $this->_pdo;
	}

	/**
	 * Return given value with quotes
	 *
	 * @param string $val Value
	 * @return string Value with quotes
	 */
	public function quote($val)
	{
		return "'$val'";
	}

	/**
	 * Return given value with backticks
	 *
	 * @param string $val Value
	 * @return string Value with backticks
	 */
	public function btick($val)
	{
		return "`$val`";
	}

	/**
	 * Initiates a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function beginTransaction()
	{
		return $this->_pdo->beginTransaction();
	}

	/**
	 * Commits a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function commit()
	{
		return $this->_pdo->commit();
	}

	/**
	 * Rolls back a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function rollBack()
	{
		return $this->_pdo->rollBack();
	}

	/**
	 * Checks if inside a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function inTransaction()
	{
		return $this->_pdo->inTransaction();
	}

	/**
	 * Get last insert ID
	 *
	 * @return string Last insert ID
	 */
	public function lastInsertId()
	{
		return $this->_pdo->lastInsertId();
	}

	/**
	 * Fetch all data by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array SQL result
	 */
	public function fetchAll($sql)
	{
		return $this->_prepare($sql)->_execute()->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch all data by SQL statement and merge by field
	 *
	 * @param string $sql SQL statement
	 * @param string $key Optional | array key
	 * @return array SQL result
	 */
	public function fetchAssoc($sql, $key = 'id')
	{
		// Raw result data
		$data = $this->_prepare($sql)->_execute()->fetchAll(PDO::FETCH_ASSOC);

		$result = array();
		if (!empty($data) && isset($data[0][$key])) {
			foreach ($data as $d) {
				$result[$d[$key]] = $d;
			}
		} else {
			$result = $data;
		}

		return $result;
	}

	/**
	 * Fetch row by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array SQL result
	 */
	public function fetchRow($sql)
	{
		return $this->_prepare($sql)->_execute()->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch single value by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return mixed Result value
	 */
	public function fetchOne($sql)
	{
		$result = $this->_prepare($sql)->_execute()->fetch(PDO::FETCH_NUM);
		return isset($result[0]) ? $result[0] : null;
	}

	/**
	 * Executes an SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array|bool|mixed|null SQL result
	 * @throws Exception
	 */
	public function query($sql)
	{
		try {
			/*** @var $result PDOStatement */
			$result = $this->_pdo->query($sql);
		} catch (PDOException $e) {
			throw new Exception('PDO Mysql statement error: ' . $e->getMessage(), $e->getCode());
		}

		$columnCount = $result->columnCount();
		$rowCount = $result->rowCount();

		// If statment is as SELECT statement
		if ($columnCount > 0) {
			// Equal to fetchOne
			if ($columnCount === 1 && $rowCount === 1) {
				$res = $result->fetch(PDO::FETCH_NUM);
				return isset($res[0]) ? $res[0] : null;

			// Equal to fetchRow
			} else if ($columnCount > 1 && $rowCount === 1) {
				return $result->fetch(PDO::FETCH_ASSOC);

			// Equal to fetchAll
			} else {
				return $result->fetchAll(PDO::FETCH_ASSOC);
			}
		} else {
			// No result
			return true;
		}
	}

	/**
	 * Insert given data into database
	 *
	 * @param string $table DB table name
	 * @param array $data Data to insert
	 * @return int Last insert ID
	 */
	public function insert($table, $data)
	{
		$keys = array();
		$values = array();

		foreach ($data as $key => $val) {
			$keys[] = $this->btick($key);
			if ($val instanceof OneExpr) {
				$values[] = $val;
				unset($data[$key]);
			} else {
				$values[] = '?';
			}
		}

		$query = 'INSERT INTO ' . $this->btick($table)
			. ' (' . implode(', ', $keys) . ')'
			. ' VALUES (' . implode(', ', $values) . ')';

		$this->_prepare($query)->_bindParams(array_values($data))->_execute();
		return $this->_pdo->lastInsertId();
	}

	/**
	 * Do a multi insert
	 *
	 * TODO: try binding for multiple rows
	 */
	public function multiInsert($table, $keys, $data)
	{}

	/**
	 * ON DUPLICATE KEY UPDATE
	 *
	 * TODO: build on duplicate key update method
	 */
	public function save($table, $data)
	{}

	/**
	 * Update data by given condition
	 *
	 * @param string $table DB table name
	 * @param array $data Data to update
	 * @param array $where Update condition
	 */
	public function update($table, $data, $where = array())
	{
		$query = 'UPDATE ' . $this->btick($table) . ' SET ';

		$par = array();
		foreach ($data as $key => $val) {
			if ($val instanceof OneExpr) {
				$par[] = $this->btick($key) . ' = ' . $val;
				unset($data[$key]);
			} else {
				$par[] = $this->btick($key) . ' = ?';
			}
		}
		$query .= implode(', ', $par);
		$this->_buildWhere($where, $query);

		$params = array_merge(
			array_values($data),
			array_values($where)
		);

		$this->_prepare($query)->_bindParams($params)->_execute();
	}

	/**
	 * Delete from database table
	 *
	 * @param string $table DB table name
	 * @param array $where Delete condition
	 */
	public function delete($table, $where = array())
	{
		$query = 'DELETE FROM ' . $this->btick($table);
		$this->_buildWhere($where, $query);

		$this->_prepare($query);
		if (!empty($where)) {
			$this->_bindParams(array_values($where));
		}

		$this->_execute();
	}

	/**
	 * Truncate database table
	 *
	 * @param string $table DB table name
	 */
	public function truncate($table)
	{
		$this->_prepare('TRUNCATE TABLE ' . $this->btick($table))->_execute();
	}

	/**
	 * Drop database table
	 *
	 * @param string $table DB table name
	 */
	public function drop($table)
	{
		$this->_prepare('DROP TABLE ' . $this->btick($table))->_execute();
	}
}


/**
 * OneDB database expression
 */
class OneExpr
{
	/**
	 * @var string Database expression
	 */
	public $expr;

	/**
	 * Expression constructor
	 *
	 * @param string $expr Database expression
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
	}

	/**
	 * Magic to string method
	 *
	 * @return string Database expression
	 */
	public function __toString()
	{
		return $this->expr;
	}
}