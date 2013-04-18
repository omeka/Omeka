<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Records that implement this interface can use the default API controller.
 * 
 * @package Omeka\Api
 */
interface Omeka_Api_RecordInterface
{
    /**
     * Return the REST representation of this record.
     * 
     * @return array
     */
    public function getRepresentation();
}
