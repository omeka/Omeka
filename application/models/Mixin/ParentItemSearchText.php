<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Keep the parent item's search text in sync with its file.
 *
 * When file texts are configured to be included in item search texts, a file
 * that is saved or deleted rebuilds its item's search text so a search
 * matching the file's texts will find the item.
 *
 * This mixin must be pushed after Mixin_ElementText so the file's element
 * texts are already persisted when the item's search text is rebuilt. The
 * rebuild itself is provided by Mixin_FileSearchTexts, mixed into Item.
 *
 * @see Mixin_FileSearchTexts
 * @package Omeka\Record\Mixin
 */
class Mixin_ParentItemSearchText extends Omeka_Record_Mixin_AbstractMixin
{
    public function afterSave($args)
    {
        $this->_rebuildItemSearchText();
    }

    public function afterDelete()
    {
        $this->_rebuildItemSearchText();
    }

    /**
     * Rebuild the parent item's search text.
     */
    private function _rebuildItemSearchText()
    {
        if (get_option('search_include_file_texts')
            && ($item = $this->_record->getItem())
        ) {
            $item->rebuildSearchText();
        }
    }
}
