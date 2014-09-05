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

		// Init test data and perform test on query method
		self::$_db->query(
			'CREATE TABLE IF NOT EXISTS ' . self::$_table . ' ('
			. 'id INT(9) NOT NULL PRIMARY KEY AUTO_INCREMENT,'
			. 'name VARCHAR(50) NOT NULL,'
			. 'email VARCHAR(50) NOT NULL,'
			. 'tel VARCHAR(30) NOT NULL'
			. ') ENGINE = InnoDB'
		);
	}

	public static function tearDownAfterClass()
	{
		self::$_db->query('DROP TABLE IF EXISTS ' . self::$_table);
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
		$aID = self::$_db->insert(self::$_table, array(
			'name'  => 'John Doe',
			'email' => 'jd@jd.com',
			'tel'   => 55555555
		));

		$this->assertTrue(is_int($aID) && $aID > 0);

		$bID = self::$_db->insert(self::$_table, array(
			'name'  => 'Skywalker',
			'email' => 'sw@sw.com',
			'tel'   => 44444444
		));

		$this->assertTrue(is_int($bID) && $bID > 0);
	}

	public function testMultiInsert()
	{
		self::$_db->multiInsert(self::$_table,
			array('name', 'email', 'tel'),
			array(
				array(
					'John Doe',
					'john@doe.com',
					12345678
				),
				array(
					'John Smith',
					'john@smith.com',
					11223344
				),
				array(
					'Jack Smith',
					'jack@smith.com',
					87654321
				)
			)
		);

		$result = self::$_db->fetchRow(
			'SELECT * FROM ' . self::$_table . ' WHERE tel = 87654321'
		);

		// Check if last value was inserted successfully
		$this->assertEquals('jack@smith.com', $result['email']);
	}

	public function testSave()
	{
		$testTel = '22222222';

		$tmpId = self::$_db->save(self::$_table, array(
			'name'  => 'Bill Gates',
			'email' => 'bg@microsoft.com',
			'tel'   => '11111111'
		));

		$id = self::$_db->save(self::$_table, array(
			'id'    => $tmpId,
			'name'  => 'Bill Gates',
			'email' => 'bg@microsoft.com',
			'tel'   => $testTel
		));

		$this->assertSame($tmpId, $id);

		$tel = self::$_db->fetchOne(
			'SELECT tel FROM ' . self::$_table . ' WHERE id = ' . $tmpId
		);

		$this->assertEquals($tel, $testTel);
	}

	public function testUpdate()
	{
		$testName = 'Steve Jobs';

		self::$_db->update(
			self::$_table,
			array('name' => $testName),
			array('id = ?' => 1)
		);

		$result = self::$_db->fetchRow(
			'SELECT * FROM ' . self::$_table . ' WHERE id = 1'
		);

		$this->assertEquals($testName, $result['name']);
	}

	public function testFetchAll()
	{
		$result = self::$_db->fetchAll(
			'SELECT * FROM ' . self::$_table
		);

		$this->assertTrue(is_array($result[0]));
		$this->assertArrayHasKey('name', $result[0]);
	}

	public function testFetchAssoc()
	{
		$result = self::$_db->fetchAssoc(
			'SELECT * FROM ' . self::$_table
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
			'SELECT * FROM ' . self::$_table . ' WHERE id = 1'
		);

		$this->assertArrayHasKey('name', $result);
	}

	public function testFetchOne()
	{
		$name = 'Steve Jobs';

		$result = self::$_db->fetchOne(
			'SELECT name FROM ' . self::$_table . ' WHERE id = 1'
		);

		$this->assertEquals($name, $result);
	}

	public function testQuery()
	{
		$testName = 'Steve Jobs';

		// Perform tests on fetching modes
		$fetchAll = self::$_db->query(
			'SELECT * FROM ' . self::$_table
		);
		$fetchRow = self::$_db->query(
			'SELECT * FROM ' . self::$_table . ' WHERE id = 1'
		);
		$fetchOne = self::$_db->query(
			'SELECT name FROM ' . self::$_table  . ' WHERE id = 1'
		);

		$this->assertTrue(is_array($fetchAll[0]));
		$this->assertArrayHasKey('name', $fetchAll[0]);
		$this->assertEquals($testName, $fetchRow['name']);
		$this->assertEquals($testName, $fetchOne);
	}

	public function testDelete()
	{
		self::$_db->delete(
			self::$_table,
			array('name = ?' => 'Skywalker')
		);

		$result = self::$_db->fetchOne(
			'SELECT name FROM ' . self::$_table . ' WHERE id = 2'
		);

		$this->assertNull($result);
	}

	public function testTruncte()
	{
		self::$_db->truncate(self::$_table);

		$result = self::$_db->fetchAll('SELECT * FROM ' . self::$_table);
		$this->assertEmpty($result);
	}

	public function testDescribe()
	{
		$result = self::$_db->describe(self::$_table);

		$realKeys = array('id', 'name', 'email', 'tel');
		$resultKeys = array_keys($result);

		$this->assertSame(
			array_diff($realKeys, $resultKeys),
			array_diff($resultKeys, $realKeys)
		);
	}

	public function testDrop()
	{
		self::$_db->drop(self::$_table);
	}
}