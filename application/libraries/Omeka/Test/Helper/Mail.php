<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Test_Helper_Mail
{   
    /**
     * @var string Path to the fakemail directory.
     */
    private $_fakemailDir;
    
    /**
     * @param string $fakemailDir Real path to the fakemail storage directory.
     */
    public function __construct($fakemailDir)
    {
        if (!is_dir($fakemailDir) || !is_writable($fakemailDir)) {
            throw new RuntimeException("Fakemail must be set up in test config.ini.");
        }
        $this->_fakemailDir = $fakemailDir;
    }
    
    /**
     * Configure an instance of the Mail helper using the registered test_config.
     */
    public static function factory()
    {
        $testConfig = Zend_Registry::get('test_config');
        $fakemailDir = $testConfig->paths->fakemaildir;
        $helper = new self($fakemailDir);
        return $helper;
    }
    
    /**
     * Empty the fakemail storage directory between test runs.
     */
    public function reset()
    {
        foreach ($this->_getIterator() as $file) {
            if ($this->_isMailFile($file)) {
                unlink($file->getRealPath());                
            }
        }
    }
    
    private function _getIterator()
    {
        return new DirectoryIterator($this->_fakemailDir);
    }
    
    private function _isMailFile(SplFileInfo $file)
    {
        return $file->isFile() && $file->isWritable(); // && $file->getFilename() != '';
    }
    
    /**
     * Return the text of the n'th email that was sent during the test.
     * 
     * Note that this will not return correct results if reset() was not 
     * invoked between test runs.
     */
    public function getMailText($index = 0)
    {
        $mails = array();
        foreach ($this->_getIterator() as $file) {
            if ($this->_isMailFile($file)) {
                $mails[] = $file->getRealPath();
            }
        }
        if (!isset($mails[$index])) {
            throw new InvalidArgumentException("No mail exists at index: $index.");
        }
        return file_get_contents($mails[$index]);
    }
}
