<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

 
/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';

/**
 * Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';


/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_File extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface 
{
    
    // ------------------
    // --- Properties ---
    // ------------------
       
    /**
     * Available options
     * 
     * =====> (string) cacheDir :
     * - Directory where to put the cache files
     * 
     * =====> (boolean) fileLocking :
     * - Enable / disable fileLocking
     * - Can avoid cache corruption under bad circumstances but it doesn't work on multithread
     * webservers and on NFS filesystems for example
     * 
     * =====> (boolean) readControl :
     * - Enable / disable read control
     * - If enabled, a control key is embeded in cache file and this key is compared with the one
     * calculated after the reading.
     * 
     * =====> (string) readControlType :
     * - Type of read control (only if read control is enabled). Available values are :
     *   'md5' for a md5 hash control (best but slowest)
     *   'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
     *   'strlen' for a length only test (fastest)
     *   
     * =====> (int) hashedDirectoryLevel :
     * - Hashed directory level
     * - Set the hashed directory structure level. 0 means "no hashed directory 
     * structure", 1 means "one level of directory", 2 means "two levels"... 
     * This option can speed up the cache only when you have many thousands of 
     * cache file. Only specific benchs can help you to choose the perfect value 
     * for you. Maybe, 1 or 2 is a good start.
     * 
     * =====> (int) hashedDirectoryUmask :
     * - Umask for hashed directory structure
     * 
     * =====> (string) fileNamePrefix :
     * - prefix for cache files 
     * - be really carefull with this option because a too generic value in a system cache dir
     *   (like /tmp) can cause disasters when cleaning the cache
     * 
     * @var array available options
     */
    protected $_options = array(
        'cacheDir' => '/tmp',
        'fileLocking' => true,
        'readControl' => true,
        'readControlType' => 'crc32',
        'hashedDirectoryLevel' => 0,
        'hashedDirectoryUmask' => 0700,
        'fileNamePrefix' => 'zend_cache'
    );
    
     
    // ----------------------
    // --- Public methods ---
    // ----------------------
    
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {   
        parent::__construct($options);
        if (isset($options['cacheDir'])) { // particular case for this option
            $this->setCacheDir($options['cacheDir']);
        } else {
            $this->_options['cacheDir'] = self::getTmpDir() . DIRECTORY_SEPARATOR;
        }
    }  
    
    /**
     * Set the cacheDir (particular case of setOption() method)
     * 
     * @param mixed $value
     */
    public function setCacheDir($value)
    {
        // add a trailing DIRECTORY_SEPARATOR if necessary 
        $value = rtrim($value, '\\/') . DIRECTORY_SEPARATOR;
        $this->setOption('cacheDir', $value);
    }
       
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false) 
    {
        clearstatcache();
        $file = $this->_file($id);
        if (($doNotTestCacheValidity) || (is_null($this->_directives['lifeTime']))) {
            if (!(@file_exists($file))) {
                // We do not test cache validity but there is no file available
                // so the cache is not hit !
                return false;
            }
        } else {
            if (!($this->_test($file))) {
                // The cache is not hit !
                return false;
            }
        }
        // There is an available cache file !
        $fp = @fopen($file, 'rb');
        if (!$fp) return false;
        if ($this->_options['fileLocking']) @flock($fp, LOCK_SH);
        $length = @filesize($file);
        $mqr = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        if ($this->_options['readControl']) {
            $hashControl = @fread($fp, 32);
            $length = $length - 32;
        } 
        if ($length) {
            $data = @fread($fp, $length);
        } else {
            $data = '';
        }
        set_magic_quotes_runtime($mqr);
        if ($this->_options['fileLocking']) @flock($fp, LOCK_UN);
        @fclose($fp);
        if ($this->_options['readControl']) {
            $hashData = self::_hash($data, $this->_options['readControlType']);
            if ($hashData != $hashControl) {
                // Problem detected by the read control !
                if ($this->_directives['logging']) {
                    Zend_Log::log('Zend_Cache_Backend_File::load() / readControl : stored hash and computed hash do not match', Zend_Log::LEVEL_WARNING);
                }
                $this->_remove($file);
                return false;    
            }
        }
        return $data;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        clearstatcache();
        $file = $this->_file($id);
        return $this->_test($file);
    }
    
    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the 
     * core not by the backend)
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array())
    {
        clearstatcache();
        $file = $this->_file($id);
        $firstTry = true;
        $result = false;
        while (1 == 1) {
            $fp = @fopen($file, "wb");
            if ($fp) {
                // we can open the file, so the directory structure is ok
                if ($this->_options['fileLocking']) @flock($fp, LOCK_EX);
                if ($this->_options['readControl']) {
                    @fwrite($fp, self::_hash($data, $this->_options['readControlType']), 32);
                }
                $mqr = get_magic_quotes_runtime();
                set_magic_quotes_runtime(0);
                @fwrite($fp, $data);
                if ($this->_options['fileLocking']) @flock($fp, LOCK_UN);
                @fclose($fp);
                set_magic_quotes_runtime($mqr);
                $result = true;
                break;
            }         
            // we can't open the file but it's maybe only the directory structure
            // which has to be built
            if ($this->_options['hashedDirectoryLevel']==0) break;
            if ((!$firstTry) || ($this->_options['hashedDirectoryLevel'] == 0)) {
                // it's not a problem of directory structure
                break;
            } 
            $firstTry = false;
            // In this case, maybe we just need to create the corresponding directory
            @mkdir($this->_path($this->_idToFileName($id)), $this->_options['hashedDirectoryUmask'], true);    
            @chmod($this->_options['hashedDirectoryUmask']); // see #ZF-320 (this line is required in some configurations)
        }
        if ($result) {
            foreach ($tags as $tag) {
                $this->_registerTag($id, $tag);
            }
        }
        return $result;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id) 
    {
        $result1 = $this->_remove($this->_file($id));
        $result2 = $this->_unregisterTag($id);
        return ($result1 && $result2);
    }
    
    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used) 
     * 'matchingTag'    => remove cache entries matching all given tags 
     *                     ($tags can be an array of strings or a single string) 
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)    
     * 
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) 
    {
        // We use this private method to hide the recursive stuff
        clearstatcache();
        return $this->_clean($this->_options['cacheDir'], $mode, $tags);
    }
    
    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     * 
     * Force a cache record to expire
     * 
     * @param string $id cache id
     */
    public function ___expire($id)
    {
        @touch($this->_file($id), time() - 2*abs($this->_directives['lifeTime'])); 
    }
    
    // -----------------------
    // --- Private methods ---
    // -----------------------
    
    /**
     * Remove a file
     * 
     * If we can't remove the file (because of locks or any problem), we will touch 
     * the file to invalidate it
     * 
     * @param string $file complete file path
     * @return boolean true if ok
     */  
    private function _remove($file)
    {
        if (!@unlink($file)) {
            # If we can't remove the file (because of locks or any problem), we will touch 
            # the file to invalidate it
            if ($this->_directives['logging']) {
                Zend_Log::log("Zend_Cache_Backend_File::_remove() : we can't remove $file => we are going to try to invalidate it", Zend_Log::LEVEL_WARNING);
            }
            if (is_null($this->_directives['lifeTime'])) return false;
            if (!file_exists($file)) return false;
            return @touch($file, time() - 2*abs($this->_directives['lifeTime'])); 
        } 
        return true;
    }
    
    /**
     * Test if the given file is available (and still valid as a cache record)
     * 
     * @param string $file
     * @return boolean mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    private function _test($file)
    {
        if (@file_exists($file)) {
            $filemtime = @filemtime($file);
            $refresh = $this->_refreshTime();
            if (is_null($refresh)) {
                return $filemtime;
            }
            if ($filemtime > $refresh) {
                return $filemtime;
            }
        }
        return false;
    }
    
    /**
     * Clean some cache records (private method used for recursive stuff)
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used) 
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags 
     *                                               ($tags can be an array of strings or a single string) 
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)    
     * 
     * @param string $dir directory to clean
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    private function _clean($dir, $mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) 
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $result = true;
        $prefix = $this->_options['fileNamePrefix'];
        $glob = @glob($dir . $prefix . '_*');
        foreach ($glob as $file)  {
            if (is_file($file)) {
                if ($mode==Zend_Cache::CLEANING_MODE_ALL) {
                    $result = ($result) && ($this->_remove($file));
                }
                if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
                    // files older than lifeTime get deleted from cache
                    if (!is_null($this->_directives['lifeTime'])) {
                        if ((time() - @filemtime($file)) > $this->_directives['lifeTime']) {
                            $result = ($result) && ($this->_remove($file));
                        }
                    }
                }
                if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
                    $matching = true;
                    $id = $this->_fileNameToId(basename($file)); 
                    if (strlen($id) > 0) {
                        foreach ($tags as $tag) {
                            if (!($this->_testTag($id, $tag))) {
                                $matching = false;
                                break;
                            }
                        }
                        if ($matching) {
                            $result = ($result) && ($this->remove($id));
                        }
                    }
                }
                if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
                    $matching = false;
                    $id = $this->_fileNameToId(basename($file));
                    if (strlen($id) > 0) {
                        foreach ($tags as $tag) {
                            if ($this->_testTag($id, $tag)) {
                                $matching = true;
                                break;
                            }
                        }
                        if (!$matching) {
                            $result = ($result) && ($this->remove($id));
                        }
                    }                               
                }
            }
            if ((is_dir($file)) and ($this->_options['hashedDirectoryLevel']>0)) {
                // Recursive call
                $result = ($result) && ($this->_clean($file . DIRECTORY_SEPARATOR, $mode, $tags));
                if ($mode=='all') {
                    // if mode=='all', we try to drop the structure too                    
                    @rmdir($file);
                }
            }
        }
        return $result;  
    }
    
    /**
     * Register a cache id with the given tag
     * 
     * @param string $id cache id
     * @param string $tag tag
     * @return boolean true if no problem
     */
    private function _registerTag($id, $tag) 
    {
        return $this->save('1', self::_tagCacheId($id, $tag));
    }
    
    /**
     * Unregister tags of a cache id
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    private function _unregisterTag($id) 
    {
        $prefix = $this->_options['fileNamePrefix'];
        $filesToRemove = @glob($this->_path($this->_idToFileName($id)) . $prefix . "_internal_$id---*" );
        $result = true;
        foreach ($filesToRemove as $file) {
            $result = $result && ($this->_remove($file));
        }    
        return $result;    
    }
    
    /**
     * Test if a cache id was saved with the given tag
     * 
     * @param string $id cache id
     * @param string $tag tag name
     * @return true if the cache id was saved with the given tag
     */
    private function _testTag($id, $tag) 
    {
        if ($this->test(self::_tagCacheId($id, $tag))) {
           return true;
        }
        return false;
    }
    
    /**
     * Compute & return the refresh time
     * 
     * @return int refresh time (unix timestamp)
     */
    private function _refreshTime() 
    {
        if (is_null($this->_directives['lifeTime'])) {
            return null;
        }
        return time() - $this->_directives['lifeTime'];
    }
    
    /**
     * Make and return a file name (with path)
     *
     * @param string $id cache id
     * @return string file name (with path)
     */  
    private function _file($id)
    {
        $fileName = $this->_idToFileName($id);
        return $this->_path($fileName) . $fileName;
    }
    
    /**
     * Transform a cache id into a file name and return it
     * 
     * @param string $id cache id
     * @return string file name
     */
    private function _idToFileName($id)
    {
        $prefix = $this->_options['fileNamePrefix'];
        return $prefix . '_' . $id;
    }
    
    /**
     * Return the complete directory path of a filename (including hashedDirectoryStructure)
     * 
     * @param string $fileName file name
     * @return string complete directory path
     */
    private function _path($fileName)
    {
        $root = $this->_options['cacheDir'];
        $prefix = $this->_options['fileNamePrefix'];
        if ($this->_options['hashedDirectoryLevel']>0) {
            if (strpos($fileName, '---') > 0) {
                // In this case, we are storing a tag
                // Let's store it in the same directory than its father
                $fileName = preg_replace('~^' . $prefix . '_internal_(.*)---(.*)$~' , $prefix . '_$1', $fileName);
            }
            $hash = md5($fileName);
            for ($i=0 ; $i<$this->_options['hashedDirectoryLevel'] ; $i++) {
                $root = $root . $prefix . '_' . substr($hash, 0, $i + 1) . DIRECTORY_SEPARATOR;
            }             
        }
        return $root;
    }
    
    /**
     * Transform a file name into cache id and return it
     * 
     * @param string $fileName file name
     * @return string cache id
     */
    private function _fileNameToId($fileName) 
    {       
        $prefix = $this->_options['fileNamePrefix'];
        if (strpos($fileName, $prefix . '_internal_') === 0) return '';
        return preg_replace('~^' . $prefix . '_(.*)$~', '$1', $fileName);
    }
    
    /**
     * Make a control key with the string containing datas
     *
     * @param string $data data
     * @param string $controlType type of control 'md5', 'crc32' or 'strlen'
     * @return string control key
     */
    static private function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            Zend_Cache::throwException("Incorrect hash function : $controlType");
        }
    }
    
    /**
     * Return a special/rerserved cache id for storing the given tag on the given id
     * 
     * @param string $id cache id
     * @param string $tag tag name
     * @return string cache id for the tag
     */
    static private function _tagCacheId($id, $tag) {
        return 'internal_' . $id . '---' . $tag;
    }
    
}
