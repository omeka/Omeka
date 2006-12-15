<?php
class Kea {
/*
 * This is just here to keep things sane for the moment
 * 
 * 
 * 
 * 
 */	
	
	public static function cleanName($name)
	{
		return preg_replace('/[^a-z0-9A-Z_-]/', '', $name);
	}
	
	public static function loadFile($dir, $file, $require = true)
	{
		$file = self::cleanName($file) . '.php';
		$fullpath = $dir . DIRECTORY_SEPARATOR . $file;

		if (!is_file($fullpath) || !is_readable($fullpath)) {
			return false;
		}
		
		if ($require) {
			require_once $fullpath;
		}
		else {
			return $fullpath;
		}
	}
}
?>