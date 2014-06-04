OneDB [![Build Status](https://travis-ci.org/cvgellhorn/OneDB.svg?branch=master)](https://travis-ci.org/cvgellhorn/OneDB)
===========

A lightweight/single file PHP database framework

#Get started
```php
// Include OneDB
require_once 'OneDB.php';

// Create OneDB instance and have fun
$database = OneDB::load(array(
    'database'  => '[database_name]',
    'user'      => '[database_username]',
    'password'  => '[database_password]'
));

// After initializing, you can always get the current instance with
$database = OneDB::load();


// Or create a new connection by name (for multiple connections)
$database = OneDB::getConnection('[connection_name]', array(
    'database'  => '[database_name]',
    'user'      => '[database_username]',
    'password'  => '[database_password]'
));

// Reload connection again later
$databaseWrite = OneDB::getConnection('[connection_name]');
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

Default values
```
'host'    => 'localhost',
'port'    => '[default_mysql_port]'
'charset' => 'utf8',
```

##Basic Usage
###Insert
Insert new records in table, always returns lastInsertId.

```php
$lastInsertId = $database->insert('user', array(
	'name'  => 'Foo Bar',
    'email' => 'foo@bar.com',
    'tel'   => 12345678
));
```