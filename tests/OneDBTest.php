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

	public function setUp()
	{
		$this->_db = OneDB::getInstance(array(
			'database'  => $GLOBALS['db_database'],
			'user'      => $GLOBALS['db_user'],
			'password'  => $GLOBALS['db_password']
		));

		$this->_db->query('CREATE TABLE ' . $this->_db->btick('test') . ' (field VARCHAR(50) NOT NULL)');
	}

	public function tearDown()
	{
		$this->_db->drop('test');
	}

	public function testGetPDO()
	{
		$this->assertInstanceOf('PDO', $this->_db->getPDO());
	}
} 