<?php
/**
 * @copyright Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for file storage adapters.
 *
 * Classes that implement this interface handle the actual work of
 * storing and retrieving files.
 *
 * @package Omeka
 */
interface Omeka_Storage_Adapter
{
    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    function __construct(array $options = null);

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
