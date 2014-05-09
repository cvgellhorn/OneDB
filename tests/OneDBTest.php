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
			$GLOBALS['db_database'],
			$GLOBALS['db_user'],
			$GLOBALS['db_password']
		));
	}
} 