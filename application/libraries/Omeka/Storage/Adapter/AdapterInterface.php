<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for file storage adapters.
 *
 * Classes that implement this interface handle the actual work ofstoring and 
 * retrieving files.
 * 
 * @package Omeka\Storage\Adapter
 */
interface Omeka_Storage_Adapter_AdapterInterface
{
    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    function __construct(array $options = null);

    /**
     * Follow any necessary steps to set up storage prior to use.
     *
     * E.g. for the filesystem adapter, this would include creating any 
     * directories that did not already exist.  For S3, it might involve 
     * creating a new bucket if it did not exist.
     *
     * @throws Omeka_Storage_Exception
     */
    function setUp();

    /**
     * Check whether the adapter is set up correctly to be able to store
     * files.
     *
     * @return boolean
     */
    function canStore();
    
    /**
     * Move a local file to "storage."
     *
     * @param string $source Local filesystem path to file.
     * @param string $dest Destination path.
     */
    function store($source, $dest);

    /**
     * Move a file between two storage locations.
     *
     * @param string $source Original storage path.
     * @param string $dest Destination storage path.
     */
    function move($source, $dest);

    /**
     * Remove a "stored" file.
     *
     * @param string $path
     */
    function delete($path);

    /**
     * Get a URI for a "stored" file.
     *
     * @param string $path
     * @return string URI
     */
    function getUri($path);
}
