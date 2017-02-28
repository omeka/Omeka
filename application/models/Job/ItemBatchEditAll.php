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
class Job_ItemBatchEditAll extends Omeka_Job_AbstractJob
{
    protected $_table;
    protected $_aclHelper;

    private $_totalItems = 0;
    private $_totalProcessedItems = 0;
    private $_itemId = 0;

    public function perform()
    {
        // Prepare the background acl to check rights of the user for each item.
        // $this->_aclHelper = get_acl();
        $acl = Zend_Registry::get('bootstrap')->bootstrap('Acl')->acl;
        $aclChecker = new Omeka_Controller_Action_Helper_Acl($acl, $this->_user);
        Zend_Controller_Action_HelperBroker::addHelper($aclChecker);
        $this->_aclHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Acl');

        // Get the record ids filtered to Omeka_Db_Table::applySearchFilters().
        $params = $this->_options['params'];
        $this->_table = $this->_db->getTable('Item');
        $alias = $this->_table->getTableAlias();
        $select = $this->_table
            ->getSelectForFindBy($params)
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array("$alias.id"));
        $itemIds = get_db()->fetchCol($select);
        if (!$itemIds) {
            _log(__('Batch Edit All Items: No item matches the specified filters.'), Zend_Log::WARN);
            return;
        }

        $totalItems = count($itemIds);
        $this->_totalItems = $totalItems;
        _log(__(plural(
            'Batch Edit: Starting processing 1 item.',
            'Batch Edit: Starting processing %d items.',
            $this->_totalItems), $this->_totalItems),
            Zend_Log::INFO);

        $totalSuccess = 0;
        foreach ($itemIds as $id) {
            $this->_itemId = $id;
            $this->_totalProcessedItems++;

            $result = $this->_performItem($id);

            if ($result) {
                $totalSuccess++;
            }
        }

        $totalErrors = $totalItems - $totalSuccess;
        if ($totalErrors) {
            _log(__('Batch Edit: A total of %d errors occurred during the batch process of %d items.',
                    $totalErrors, $this->_totalItems),
                Zend_Log::ERR);
        }
        // No error occured.
        else {
            _log(__(plural(
                'Batch Edit: The item has been processed successfully.',
                'Batch Edit: All %d items have been processed successfully.',
                $totalItems), $totalItems),
                Zend_Log::INFO);
        }
    }

    /**
     * Check if the item can be processed, then process it if possible.
     *
     * @param int $itemId
     * @return bool
     */
    protected function _performItem($itemId)
    {
        $item = $this->_table->find($itemId);
        if (!$item) {
            $message = __('Item does not exist: it may have been deleted before processing.');
            $this->_logProcessedItem($message, Zend_Log::ERR);
            return false;
        }

        $delete = $this->_options['delete'];
        $metadata = $this->_options['metadata'];
        $custom = $this->_options['custom'];

        $message = null;

        // Check rights via acl.
        $aclHelper = $this->_aclHelper;

        if ($delete && !$aclHelper->isAllowed('delete', $item)) {
            $message = __('User is not allowed to delete this item.');
            $this->_logProcessedItem($message, Zend_Log::ERR);
            return false;
        }

        // Check to see if anything but 'tag'.
        if ($metadata && array_diff_key($metadata, array('tags' => '')) && !$aclHelper->isAllowed('edit', $item)) {
            $message = __('User is not allowed to edit this item.');
            $this->_logProcessedItem($message, Zend_Log::ERR);
            return false;
        }

        if ($metadata && array_key_exists('tags', $metadata) && !$aclHelper->isAllowed('tag', $item)) {
            $message = __('User is not allowed to tag this item.');
            $this->_logProcessedItem($message, Zend_Log::ERR);
            return false;
        }

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
            $this->_logProcessedItem($message, Zend_Log::ERR);
            return false;
        }

        // No error, so process the edition.
        if ($delete == '1') {
            $item->delete();
            $message = __('The item was successfully deleted.');
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
                $this->_logProcessedItem($message, Zend_Log::ERR);
                return false;
            }

            $message = __('The item was successfully batch-edited.');
        }

        release_object($item);

        $this->_logProcessedItem($message, Zend_Log::INFO);
        return true;
    }

    /**
     * Log a message about the current processed item.
     *
     * @param string $message
     * @param string $priority
     */
    private function _logProcessedItem($message, $priority)
    {
        _log(
            __('Batch Edit Item #%d [%d/%d]: %s',
                $this->_itemId, $this->_totalProcessedItems, $this->_totalItems, $message),
            $priority);
    }
}
