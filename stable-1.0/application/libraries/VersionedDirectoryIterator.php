<?php 
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