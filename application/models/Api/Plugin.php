<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record\Api
 */
class Api_Plugin extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST API representation for a plugin.
     *
     * @param Plugin $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array(
            'id' => $record->id,
            'url' => $this->getResourceUrl("/plugins/{$record->id}"),
            'name' => $record->name,
            'active' => (bool) $record->active,
            'version' => $record->version
        );
        return $representation;
    }
}
