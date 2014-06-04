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
	private static $_db;

	/**
	 * Test DB table
	 *
	 * @var string
	 */
	private static $_table = 'data';

	public static function setUpBeforeClass()
	{
		self::$_db = OneDB::load(array(
			'database'  => $GLOBALS['db_database'],
			'user'      => $GLOBALS['db_user'],
			'password'  => $GLOBALS['db_password']
		));

		// Init test data and also perform test on query method
		self::$_db->query(
			'CREATE TABLE IF NOT EXISTS '
			. self::$_db->btick(self::$_table). ' ('
			. 'id INT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,'
			. 'name VARCHAR(50) NOT NULL'
			. ') ENGINE = InnoDB'
		);
	}

	public static function tearDownAfterClass()
	{
		self::$_db->query(
			'DROP TABLE IF EXISTS ' . self::$_db->btick(self::$_table)
		);
	}

	public function testGetPDO()
	{
		$this->assertInstanceOf('PDO', self::$_db->getPDO());
	}

	public function testQuote()
	{
		$this->assertEquals("'test'", self::$_db->quote('test'));
	}

	public function testBtick()
	{
		$this->assertEquals("`test`", self::$_db->btick('test'));
	}

	public function testInsert()
	{
		$id = self::$_db->insert(self::$_table, array(
			'name' => 'John Doe'
		));

		$this->assertTrue(is_int($id) && $id > 0);


		$id = self::$_db->insert(self::$_table, array(
			'name' => 'Skywalker'
		));

		$this->assertTrue(is_int($id) && $id > 0);
	}

	public function testUpdate()
	{
		$name = 'Steve Jobs';

		self::$_db->update(
			self::$_table,
			array('name' => $name),
			array('id = ?' => 1)
		);

		$result = self::$_db->fetchRow(
			'SELECT * FROM ' . self::$_db->btick(self::$_table)
			. ' WHERE ' . self::$_db->btick('id') . ' = 1'
		);

		$this->assertEquals($name, $result['name']);
	}

	public function testFetchAll()
	{
		$result = self::$_db->fetchAll(
			'SELECT * FROM ' . self::$_db->btick(self::$_table)
		);

		$this->assertTrue(is_array($result[0]));
		$this->assertArrayHasKey('name', $result[0]);
	}

	public function testFetchAssoc()
	{
		$result = self::$_db->fetchAssoc(
			'SELECT * FROM ' . self::$_db->btick(self::$_table)
		);

		$this->assertTrue(count($result) > 0);
		foreach ($result as $id => $row) {
			// Compare int with string
			$this->assertTrue($id == $row['id']);
		}
	}

	public function testFetchRow()
	{
		$result = self::$_db->fetchRow(
			'SELECT * FROM ' . self::$_db->btick(self::$_table)
			. ' WHERE ' . self::$_db->btick('id') . ' = 1'
		);

		$this->assertArrayHasKey('name', $result);
	}

	public function testFetchOne()
	{
		$name = 'Steve Jobs';

		$result = self::$_db->fetchOne(
			'SELECT ' . self::$_db->btick('name') . ' FROM ' . self::$_db->btick(self::$_table)
			. ' WHERE ' . self::$_db->btick('id') . ' = 1'
		);

		$this->assertEquals($name, $result);
	}

	public function testQuery()
	{
		// insert, update, delete
		// all fetching modes
	}

	public function testDelete()
	{
		self::$_db->delete(
			self::$_table,
			array('name = ?' => 'Skywalker')
		);

		$result = self::$_db->fetchOne(
			'SELECT ' . self::$_db->btick('name') . ' FROM ' . self::$_db->btick(self::$_table)
			. ' WHERE ' . self::$_db->btick('id') . ' = 2'
		);

		$this->assertNull($result);
	}

	public function testTruncte()
	{

	}

	public function testDrop()
	{
		self::$_db->drop(self::$_table);
	}
}