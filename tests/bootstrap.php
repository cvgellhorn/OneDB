<?php

/**
 * PHPUnit AutoLoader
 *
 * @author cvgellhorn
 */
class Autoloader
{
	/**
	 * Class loader method
	 *
	 * @param Mixed $class Class name
	 */
	public static function load($class)
	{
		$file = $class . '.php';
		if (file_exists($file)) {
			require_once($file);
		}
	}
}

// Register autoloader
spl_autoload_register(array('Autoloader', 'load'));