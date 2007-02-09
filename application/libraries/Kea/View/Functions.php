<?php
/**
 * Not quite a helper, these functions defy definition...
 * 
 * Ok so not really.  All they do is help theme creators
 * do some pretty basic things like include images, css or js files.
 * 
 * They purposely do not use objects in order to simplify the theme
 * writer's need to understand the underlying system at work.
 * 
 * However, they make use of Zend::registry a lot, which may be a
 * speed issue in the long term.
 * 
 * @package Omeka
 * @author Nate Agrin
 */

/**
 * Echos the physical path to the theme.
 * This should be used when you need to include a file through PHP.
 */
function theme_path() {
	echo Zend::registry('theme_path');
}

/**
 * Echos the web path of the theme.
 * This should be used when you need to link in an image or other file.
 */
function web_path() {
	echo Zend::registry('theme_web');
}

function src($file, $dir, $ext = null) {
	$physical = Zend::registry('theme_path').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file;
	if ($ext !== null) {
		$physical .= '.'.$ext;
	}
	if (file_exists($physical)) {
		echo Zend::registry('theme_web').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.'.$ext;
	}
	else {
		throw new Exception('Cannot find '.$file.'.'.$ext);
	}
}

/**
 * Echos the web path (that's what's important to the browser)
 * to a javascript file.
 * $dir defaults to 'javascripts'
 * $file should not include the .js extension
 */
function script($file, $dir = 'javascripts') {
	src($file, $dir, 'js');
}

/**
 * Echos the web path to a css file
 * $dir defaults to 'css'
 * $file should not include the .css extension
 */
function css($file, $dir = 'css') {
	src($file, $dir, 'css');
}

/**
 * Echos the web path to an image file
 * $dir defaults to 'images'
 * $file SHOULD include an extension, many image exensions are possible
 */
function img($file, $dir = 'images') {
	src($file, $dir);
}

function common($file, $dir = 'common') {
	$path = Zend::registry('theme_path').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.php';
	if (file_exists($path)) {
		include $path;
	}
}

function head($file = 'header') {
	common($file);
}

function footer($file = 'footer') {
	common($file);
}

?>