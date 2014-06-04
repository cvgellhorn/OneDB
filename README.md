OneDB [![Build Status](https://travis-ci.org/cvgellhorn/OneDB.svg?branch=master)](https://travis-ci.org/cvgellhorn/OneDB)
===========

> A lightweight/single file PHP database framework

##Overview
OneDB is using the PDO extension and is based on three classes:

* <b>OneDB</b> - Main database framework
* <b>OneExpr</b> - Database expression
* <b>OneException</b> - Exception

It's also very lightweight, only around 11 kb and all packed in a single PHP file.

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
Insert new records in table, returns lastInsertId.

```php
$lastInsertId = $database->insert('user', array(
	'name'  => 'Foo Bar',
    'email' => 'foo@bar.com',
    'tel'   => 12345678
));
```