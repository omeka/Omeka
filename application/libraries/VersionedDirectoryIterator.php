<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Iterate through directories that may contain VCS metadata, i.e. .svn 
 * directories.
 * 
 * @package Omeka\Db\Migration
 */
class VersionedDirectoryIterator extends DirectoryIterator
{
    private $dirsOnly;
    public function __construct($path,$dirsOnly=true)
    {
        $this->dirsOnly = $dirsOnly;
        parent::__construct($path);
    }
    
    public function valid()
    {
        if (parent::valid()) {
            if (($this->dirsOnly && !parent::isDir()) 
                || parent::isDot() 
                || (parent::getFileName() == '.svn') ) {
                parent::next();
                return $this->valid();
            }
            return true;
        }
        return false;
    }
    
    public function current()
    {
        return parent::getFileName();
    }
    
    public function getValid()
    {
        $dirs = array();
        foreach ($this as $dir) {
            $dirs[] = $dir;
        }
        return $dirs;
    }
}
