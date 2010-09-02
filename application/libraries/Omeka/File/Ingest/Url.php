<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Ingest URLs into the Omeka archive.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @todo Alternative method that uses cURL/Zend_HTTP_Client.
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
 */
class Omeka_File_Ingest_Url extends Omeka_File_Ingest_Source
{
    /**
     * Possible transfer methods.
     *
     * These correspond with methods to call on this class.
     *
     * @var array
     */
    protected $_transferMethods = array('wget', 'copy');
    
    /**
     * Return the original name of the file to be ingested.
     *
     * @param array $fileInfo
     * @return string
     */ 
    protected function _getOriginalFilename($fileInfo)
    {
        if (!($original = parent::_getOriginalFilename($fileInfo))) {
            // Since the original file is from a URL, it is necessary to decode 
            // the URL in case it has been encoded.
            $original = urldecode($fileInfo['source']);
        }
        return $original;
    }
    
    /**
     * Fetch a file from a URL with the wget binary.
     * 
     * @param string $source Source URL.
     * @param string $destination Desination file path.
     * @return boolean True if fetch was successful, false otherwise.
     */
    protected function _wget($source, $destination)
    {
        if (!$this->_isWgetAvailable()) {
            return false;
        }
        
        // Only create the file if the URL is valid, otherwise the -O option 
        // will create an empty file, which is not expected behavior.
        $sourceArg      = escapeshellarg($source);
        $destinationArg = escapeshellarg($destination);
        $command        = "wget -O $destinationArg $sourceArg";
        exec($command, $output, $returnVar);
        
        return ($returnVar === 0);
    }
    
    /**
     * Fetch a file from a URL using PHP fopen() wrappers.
     * 
     * @param string $source Source URL.
     * @param string $destination Destination file path.
     * @return boolean True if fetch was successful, false otherwise.
     */
    protected function _copy($source, $destination)
    {
        if (!$this->_canCopyFromUrl($source)) {
            return false;
        }

        return copy($source, $destination);
    }

    /**
     * Determine if the wget binary is available on the server.
     *
     * @return boolean
     */
    protected function _isWgetAvailable()
    {
        exec('which wget', $output, $returnVar);
        return !empty($output);      
    }
    
    /**
     * Determine if the server allows URL fopen() calls.
     *
     * @param string $source Source URL.
     * @return boolean
     */
    protected function _canCopyFromUrl($source)
    {
        // Only throw an exception here because this is our fallback.
        if (!ini_get('allow_url_fopen')) {
            throw new Omeka_File_Ingest_Exception('fopen stream wrappers must be enabled in order to copy files from a URL!');
        }
        
        return true;
    }
    
    /**
     * Fetch a file from a URL.
     *
     * Delegates to individual transfer methods.
     *
     * @throws Omeka_File_Ingest_Exception
     * @param string $source Source URL.
     * @param string $destination Destination file path.
     * @param array $fileInfo
     * @return void
     */
    protected function _transfer($source, $destination, array $fileInfo)
    {
        $transferred = false;
        foreach ($this->_transferMethods as $method) {
            $classMethod = '_' . $method;
            if ($transferred = $this->$classMethod($source, $destination)) {
                break;
            }
        }
        
        // Restore the user_agent after each file transfer.
        ini_restore('user_agent');
        
        if (!$transferred) {
            throw new Omeka_File_Ingest_Exception('Could not transfer the file from "' . $source 
                              . '" to "' . $destination . '"!');
        }
    }
    
    /**
     * Ensure the source URL exists and can be read from.
     *
     * @throws Omeka_File_Ingest_InvalidException
     * @param string $source Source URL.
     * @param array $info File info array (unused).
     * @return void
     */
    protected function _validateSource($source, $info)
    {
        // Set an arbitrary user_agent before every file transfer to minimize 
        // the chances of 403 forbidden responses.
        ini_set('user_agent', 'Omeka/' . OMEKA_VERSION); 
        
        // FIXME: This will fail if fopen wrappers are not enabled, but it
        // executes before fopen wrappers are checked.
        if (!fopen($source, 'r')) {
            
            // Restore the user_agent before throwing the exception.
            ini_restore('user_agent');
            
            throw new Omeka_File_Ingest_InvalidException("URL is not readable or does not exist: $source");
        }
    }   
    
    /**
     * Retrieve the MIME type of a given URL.
     * 
     * Take this from the Content-type header of the request.
     * 
     * @internal If this fails or proves too fragile for some reason, a more 
     * robust (read: complicated) solution could use Zend_Http_Client.
     * In that case, it might be better to use that for the rest of the Url
     * implementation (transfer, etc.).
     * @param array $fileInfo
     * @return string
     */
    protected function _getFileMimeType($fileInfo)
    {
        $sourceUrl = $this->_getFileSource($fileInfo);
        $fp = fopen($sourceUrl, 'r');
        $meta = stream_get_meta_data($fp);
        // The stream metadata contains a 'wrapper_data' key with all the 
        // headers in it.  Extract the Content-type header from this.
        $wrapperData = current(preg_grep('/Content\-(T|t)ype\: /', $meta['wrapper_data']));
        $mimeType = $this->_stripCharsetFromMimeType(preg_replace('/Content-(T|t)ype: /', '', $wrapperData));
        return $mimeType;
    }
}
