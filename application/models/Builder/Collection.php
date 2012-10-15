<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Build a collection.
 * 
 * @package Omeka\Record\Builder
 */
class Builder_Collection extends Omeka_Record_Builder_AbstractBuilder
{
    protected $_settableProperties = array(
        'name', 
        'description', 
        'public', 
        'featured',
        'owner_id'
    );
    
    protected $_recordClass = 'Collection';
    
    /**
     * Add collectors associated with the collection.
     */
    protected function _beforeBuild(Omeka_Record_AbstractRecord $record)
    {
        $metadata = $this->getRecordMetadata();
        if (array_key_exists('collectors', $metadata)) {
            $record = $this->getRecord();
            foreach($metadata['collectors'] as $collector) {
                $record->addCollector($collector);
            }
        }
    }
}
