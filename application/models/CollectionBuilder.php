<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Build a collection.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class CollectionBuilder extends Omeka_Record_Builder
{
    protected $_settableProperties = array('name', 'description', 'public', 'featured');
    protected $_recordClass = 'Collection';
    
    /**
     * Add collectors associated with the collection.
     */
    protected function _beforeBuild()
    {
        if (array_key_exists('collectors', $this->_metadataOptions)) {
            foreach($this->_metadataOptions['collectors'] as $collector) {
                $this->_record->addCollector($collector);
            }
        }
    }
}
