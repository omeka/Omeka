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
        $editAll = $this->_options['editAll'];

        if ($editAll) {
            foreach ($itemIds as $id) {
                $this->_performItem($id, $delete, $metadata, $custom);
            }
            return;
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

    /**
     * Check if the item can be processed, then process it if possible.
     *
     * @param integer $itemId
     * @param boolean $delete
     * @param array $metadata
     * @param array $custom
     * @return boolean
     */
    protected function _performItem($itemId, $delete, $metadata, $custom)
    {
        $item = $this->_db->getTable('Item')->find($itemId);
        if (!$item) {
            $message = __('Item does not exist: it may have been deleted before processing.');
            _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
            return false;
        }

        $message = null;

        /*
        // TODO Check rights of the user to process each item in background.
        // Check rights via acl.
        $aclHelper = get_acl();

        if ($delete && !$aclHelper->isAllowed('delete', $item)) {
            $message = __('User is not allowed to delete this item.');
            _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
            return false;
        }

        // Check to see if anything but 'tag'.
        if ($metadata && array_diff_key($metadata, array('tags' => '')) && !$aclHelper->isAllowed('edit', $item)) {
            $message = __('User is not allowed to edit this item.');
            _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
            return false;
        }

        if ($metadata && array_key_exists('tags', $metadata) && !$aclHelper->isAllowed('tag', $item)) {
            $message = __('User is not allowed to tag this item.');
            _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
            return false;
        }
        */

        // Check errors set by plugins filters.
        $message = apply_filters(
            'items_batch_edit_error',
            $message,
            array(
                'item_ids' => array($itemId),
                'metadata' => $metadata,
                'custom' => $custom,
            )
        );
        if ($message) {
            _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
            return false;
        }

        // No error, so process the edition.
        if ($delete == '1') {
            $item->delete();
        } else {
            foreach ($metadata as $key => $value) {
                if ($value === '') {
                    unset($metadata[$key]);
                }
            }
            try {
                update_item($item, $metadata);
                fire_plugin_hook('items_batch_edit_custom',
                    array('item' => $item, 'custom' => $custom));
            } catch (Exception $e) {
                $message = __('An error occured when the item was updated: %s', $e->getMessage());
                _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::ERR);
                return false;
            }
        }

        release_object($item);

        $message = __('The item was successfully batch-edited.');
        _log(__('Batch Edit Item #%d: %s', $itemId, $message), Zend_Log::INFO);
        return true;
    }
}
