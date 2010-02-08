<?php
/**
 * Contains static methods in the 'Omeka' namespace.  This is outdated
 * and may be removed in future.
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @package Omeka
 **/
final class Omeka
{
    static function autoload($classname)
    {
        if (class_exists($classname, false)) {
            return false;
        }
        
        $basePaths = explode(PATH_SEPARATOR, get_include_path());
        $classPath = str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
        
        foreach ($basePaths as $basePath) {
            $filePath = $basePath . DIRECTORY_SEPARATOR . $classPath;
            if (file_exists($filePath)) {
                require_once $filePath;
                return;
            }
        }

        return false;
    }
}