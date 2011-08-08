<?php
/**
 * @copyright Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for batch item edit job.
 *
 * @package Omeka
 */
class Item_BatchEditJob extends Omeka_JobAbstract {
    
    public function perform()
    {
        if (!($itemIds = $this->_options['itemIds'])) {
            return;
        }
        $delete = $this->_options['delete'];
        $metadata = $this->_options['metadata'];
        $custom = $this->_options['custom'];

        if ($user = $this->getUser()) {
            $metadata['tag_entity'] = $user->getEntity();
        }
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
                fire_plugin_hook('items_batch_edit_custom', $item, $custom);
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
