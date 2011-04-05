<?php
/**
 * @copyright Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Ingest URLs into the Omeka archive.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 */
class Omeka_File_Ingest_Url extends Omeka_File_Ingest_Source
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
            // Since the original file is from a URL, it is necessary to decode 
            // the URL in case it has been encoded.
            $original = urldecode($fileInfo['source']);
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
            $client->setStream($destination);
            $client->request('GET');
        } catch (Zend_Http_Client_Exception $e) {
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
        $uri = Zend_Uri::factory($source);

        if (!($uri && $uri->valid())) {
            throw new Omeka_File_Ingest_InvalidException("$source is not a valid URL.");
        }

        $client = $this->_getHttpClient($source);
        $response = $client->request('HEAD');
        if ($response->isError()) {
            $code = $response->getStatus();
            throw new Omeka_File_Ingest_InvalidException("$source cannot be read. The server returned code $code.");
        }
    }
