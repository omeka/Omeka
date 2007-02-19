<?php
/**
 * @package Kea
 * @author Nate Agrin
 */
final class Kea
{
	static function autoload($classname)
	{
		if (class_exists($classname)) {
			return false;
		}

		$path = dirname(__FILE__);
		$class = $path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR,$classname) . '.php';

		if (file_exists($class)) {
			require_once $class;
			return;
		}

		return false;
	}
}
?>