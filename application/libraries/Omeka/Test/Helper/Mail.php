<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Encapsulates testing functionality for email.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Test_Helper_Mail
{   
    /**
     * Path to the mail storage directory.
     *
     * @var string
     */
    private $_path;
    
    /**
     * @param string $path Real path to the mail storage directory.
     */
    public function __construct($path)
    {
        if (!is_writable($path)) {
            throw new RuntimeException(__('The mail path %s must be writable by this user.', $path));
        }
        
        $this->_path = $path;
    }
    
    /**
     * Configure an instance of the Mail helper using the registered test_config.
     *
     * @return Omeka_Test_Helper_Mail
     */
    public static function factory()
    {
        $path = Zend_Registry::get('test_mail_dir');
        $helper = new self($path);
        return $helper;
    }
    
    /**
     * Get an iterator over the fakemail directory.
     *
     * @return DirectoryIterator
     */
    private function _getIterator()
    {
        return new DirectoryIterator($this->_path);
    }
    
    /**
     * Check if a directory entry is a mail message file.
     *
     * @param SplFileInfo $file
     * @return boolean
     */
    private function _isMailFile(SplFileInfo $file)
    {
        return $file->isFile() && $file->isWritable(); // && $file->getFilename() != '';
    }
    
    /**
     * Return the text of the n'th email that was sent during the test.
     * 
     * Note that this will not return correct results if reset() was not 
     * invoked between test runs.
     *
     * @param integer $index
     * @return string
     */
    public function getMailText($index = 0)
    {
        $mails = array();
        foreach ($this->_getIterator() as $file) {
            if ($this->_isMailFile($file)) {
                $mails[] = $file->getRealPath();
            }
        }

        sort($mails);
        
        if (!isset($mails[$index])) {
            throw new InvalidArgumentException(__("No mail exists at index: %s.", strval($index)));
        }
        return file_get_contents($mails[$index]);
    }

    /**
     * The number of mails that have been sent.
     */
    public function count()
    {
        // Warning, ugly alert.
        $count = 0;
        foreach ($this->_getIterator() as $file) {
            if ($this->_isMailFile($file)) {
                $count++;
            }
        }
        return $count;
    }
}
