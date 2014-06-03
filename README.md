OneDB [![Build Status](https://travis-ci.org/cvgellhorn/OneDB.svg?branch=master)](https://travis-ci.org/cvgellhorn/OneDB)
===========

A lightweight/single file PHP database framework

#Get started
```php
// Include OneDB
require_once 'OneDB.php';

// Create OneDB instance and have fun
$database = OneDB::getInstance(array(
    'database'  => '[database_name]',
    'user'      => '[database_username]',
    'password'  => '[database_password]'
));

//Or create a new connection by name (for multiple connections)
$database = OneDB::getConnection('[connection_name]', array(
    'database'  => '[database_name]',
    'user'      => '[database_username]',
    'password'  => '[database_password]'
));

// Reload connection again later
$databaseWrite = OneDB::getConnection('[connection_name]');
```