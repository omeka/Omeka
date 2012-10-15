<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Job
 */
class Job_ItemBatchEdit extends Omeka_Job_AbstractJob
{
    
    public function perform()
    {
        if (!($itemIds = $this->_options['itemIds'])) {
            return;
        }
        $delete = $this->_options['delete'];
        $metadata = $this->_options['metadata'];
        $custom = $this->_options['custom'];

        foreach ($itemIds as $id) {
            $item = $this->_getItem($id);
            if ($delete == '1') {
                $item->delete();
            } else {
                foreach ($metadata as $key => $value) {
                    if ($value === '') {
                        unset($metadata[$key]);
                    }
                }
                update_item($item, $metadata);
                fire_plugin_hook('items_batch_edit_custom', 
                                 array('item' => $item, 'custom' => $custom));
            }
            release_object($item);
        }
    }

    private function _getItem($id)
    {
        $item = $this->_db->getTable('Item')->find($id);
        if (!$item) {
            throw new RuntimeException("Item with ID={$this->_options['itemId']} does not exist");
        }
        return $item;
    }
}
