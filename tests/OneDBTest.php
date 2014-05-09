<?php

/**
 * OneDBTest
 *
 * Date: 09.05.14
 * Time: 19:56
 * User: cgellhorn
 */
class OneDBTest extends PHPUnit_Framework_TestCase
{
	/**
	 * OneDB Adapter
	 *
	 * @var OneDB
	 */
	private $_db;

	/**
	 * Test DB table
	 *
	 * @var string
	 */
	private $_table = 'data';

	public function setUp()
	{
		$this->_db = OneDB::getInstance(array(
			'database'  => $GLOBALS['db_database'],
			'user'      => $GLOBALS['db_user'],
			'password'  => $GLOBALS['db_password']
		));

		// Init test data and also perform test on query method
		$this->_db->query(
			'CREATE TABLE IF NOT EXISTS '
			. $this->_db->btick($this->_table). ' ('
			. 'id INT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,'
			. 'name VARCHAR(50) NOT NULL'
			. ') ENGINE = InnoDB'
		);
	}

	public function tearDown()
	{
		$this->_db->drop($this->_table);
	}

	public function testGetPDO()
	{
		$this->assertInstanceOf('PDO', $this->_db->getPDO());
	}

	public function testQuote()
	{
		$this->assertEquals("'test'", $this->_db->quote('test'));
	}

	public function testBtick()
	{
		$this->assertEquals("`test`", $this->_db->btick('test'));
	}

	public function testInsert()
	{
		$id = $this->_db->insert($this->_table, array(
			'name' => 'John Doe'
		));

		$this->assertTrue(is_int($id) && $id > 0);
	}

	public function testUpdate()
	{
		$name = 'Steve Jobs';

		$this->_db->update(
			$this->_table,
			array('name' => $name),
			array('id = ?' => 1)
		);

		$result = $this->_db->fetchRow(
			'SELECT * FROM ' . $this->_db->btick($this->_table)
			. ' WHERE ' . $this->_db->btick('id') . ' = 1'
		);

		$this->assertEquals($name, $result['name']);
	}

	public function testFetchAll()
	{
		$result = $this->_db->fetchAll(
			'SELECT * FROM ' . $this->_db->btick($this->_table)
		);

		$this->assertTrue(is_array($result[0]));
		$this->assertArrayHasKey('name', $result[0]);
	}
} 