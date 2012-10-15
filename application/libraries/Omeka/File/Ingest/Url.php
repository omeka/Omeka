<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Ingest URLs into Omeka.
 * 
 * @package Omeka\File\Ingest
 */
class Omeka_File_Ingest_Url extends Omeka_File_Ingest_AbstractSourceIngest
{
    /**
     * Return the original filename.
     *
     * @param array $fileInfo
     * @return string
     */
    protected function _getOriginalFilename($fileInfo)
    {
        if (!($original = parent::_getOriginalFilename($fileInfo))) {
            $url = $fileInfo['source'];
            
            //gets rid of the query string, if it exists
            if ($index = strpos($url, '?')) {
                $url = substr($url, 0, $index);
            }

            // Since the original file is from a URL, it is necessary to
            // decode the URL in case it has been encoded.
            $original = urldecode($url);
        }
        return $original;
    }

    /**
     * Get a HTTP client for retrieving the given file.
     *
     * @param string $source Source URI.
     * @return Zend_Http_Client
     */
    protected function _getHttpClient($source)
    {
        return $this->_client = new Zend_Http_Client($source, array(
                'useragent' => 'Omeka/' . OMEKA_VERSION));
    }

    /**
     * Fetch a file from a URL.
     *
     * @throws Omeka_File_Ingest_Exception
     * @param string $source Source URL.
     * @param string $destination Destination file path.
     * @param array $fileInfo
     * @return void
     */
    protected function _transfer($source, $destination, array $fileInfo)
    {
        try {
            $client = $this->_getHttpClient($source);
            $client->setHeaders('Accept-encoding', 'identity');
            $client->setStream($destination);
            $response = $client->request('GET');
        } catch (Zend_Http_Client_Exception $e) {
            throw new Omeka_File_Ingest_Exception(
                'Could not transfer the file from "' . $source
                . '" to "' . $destination . '": ' . $e->getMessage());
        }
    
        if ($response->isError()) {
            $code = $response->getStatus();
            $msg = "The server returned code '$code'";
            throw new Omeka_File_Ingest_Exception(
                "'$source' cannot be read: " . $msg);
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
        try {
            $uri = Zend_Uri::factory($source);
            $uriIsValid = $uri->valid();
        } catch (Zend_Uri_Exception $e) {
            $uriIsValid = false;
            $uri = false;
        }

        if (!($uri && $uriIsValid)) {
            throw new Omeka_File_Ingest_InvalidException("$source is not a valid URL.");
        }
    }
}
