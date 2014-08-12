OneDB [![Build Status](https://travis-ci.org/cvgellhorn/OneDB.svg?branch=master)](https://travis-ci.org/cvgellhorn/OneDB)
===========

> A lightweight/single file PHP database framework

##Overview
OneDB is using the PDO extension and is based on three classes:

* <b>OneDB</b> - Main database framework
* <b>OneExpr</b> - Database expression
* <b>OneException</b> - Exception

All tests are based on the [PHPUnit](http://phpunit.de/) testing framework. You can easily set up your own phpunit.xml, for local unit testing. It's also very lightweight, only around 13 kb and all packed in a single PHP file.

##Getting started
```php
// Include OneDB
require_once 'OneDB.php';

// Create OneDB instance and have fun
$database = OneDB::load(array(
    'database'  => 'application',
    'user'      => 'root',
    'password'  => 'admin123#'
));

// After initializing, you can always get the current instance with
$database = OneDB::load();


// Or create a new connection by name (for multiple connections)
$dbWrite = OneDB::getConnection('write', array(
    'database'  => 'application',
    'user'      => 'root',
    'password'  => 'admin123#'
));

// Reload connection again later
$dbWrite = OneDB::getConnection('write');
```

##Configuration
You can also set the database host, port and charset.
```php
$database = OneDB::load(array(
	'host'      => 'sql.mydomain.com',
    'port'      => '3307',
    'charset'   => 'utf16',
    'database'  => 'application',
    'user'      => 'root',
    'password'  => 'admin123#'
));
```

Default settings
```php
'host'    => 'localhost'
'port'    => '[default_mysql_port]'
'charset' => 'utf8'
```

##Basic Usage
###Insert
Insert new records in table, returns LAST_INSERT_ID.

```php
insert($table : string, $data : array)
```

Example:
```php
$lastInsertId = $database->insert('user', array(
	'name'  => 'John Doe',
    'email' => 'john@doe.com',
    'tel'   => 12345678
));
```

###Update
Edit data in table. You can use any given operator in the WHERE clause to filter the records. The ? represents the placeholder for the given param.

```php
update($table : string, $data : array, [$where : array])
```

Example:
```php
$database->update(
	'user',
    array(
		'name'   => 'John Smith',
    	'email'  => 'john@smith.com',
    	'tel'    => 87654321
    ),
    array(
    	'id = ?' => 23
    )
);
```

###Delete
Remove data from table. Just as update, the ? represents the placeholder for the given param.

```php
delete($table : string, [$where : array])
```

Example:
```php
$database->delete('user', array(
	'id = ?' => 23
));
```

###Work in progress...