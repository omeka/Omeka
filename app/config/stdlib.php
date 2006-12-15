<?php
/**
 * Global functions
 * Available in all classes and themes
 */
function issetor(&$foo, $bar) {
	return isset($foo) ? $foo : $foo = $bar;
}

function notemptyor(&$foo, $bar) {
	return !empty($foo) ? $foo : $foo = $bar;
}

// in case mime_content_type doesn't exist, define it
if (!function_exists('mime_content_type')) {
	function mime_content_type($f)
	{
		return exec(trim('file -bi ' . escapeshellarg($f)));
	}
}

?>