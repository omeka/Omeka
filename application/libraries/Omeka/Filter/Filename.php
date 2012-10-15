<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Rename a file to make it suitable for inclusion in the Omeka repository.
 * 
 * @package Omeka\Filter
 */
class Omeka_Filter_Filename implements Zend_Filter_Interface
{
    /**
     * Grab the original path to the file, rename it according to our 
     * convention, and return the new path to the file.
     * 
     * @param string $value Path to the file.
     * @return string Path to the (renamed) file.
     */
    public function filter($value)
    {
        $filename = basename($value);
        $directory = dirname($value);
        $newFilename = $this->renameFile($filename);
        
        $targetPath = $directory . '/' . $newFilename;
        $result = rename($value, $targetPath);
        
        return $targetPath;
    }
    
    /**
     * Creates a new, random filename for storage in Omeka.
     * 
     * @param string $name
     * @return string
     */
    public function renameFile($name) 
    {
        $extension = strrchr($name, '.');
        $basename = md5(mt_rand() + microtime(true));        
        // Assume that extensions that do not exclusively contain alphanumeric, 
        // hyphen, and underscore characters are invalid, and remove them.
        if (preg_match('/[^a-z0-9_\-]/i', substr($extension, 1))) {
            return $basename;
        }
        return $basename . $extension;
    }
}
