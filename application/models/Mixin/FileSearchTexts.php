<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Include the search texts of an item's files in the item's search text.
 *
 * When file texts are configured to be included in item search texts, this
 * mixin adds the texts of the item's files to the item's search text so a
 * search matching a file will find the item.
 *
 * This mixin must be pushed before Mixin_Search so the file texts are
 * included when the search text is saved.
 *
 * @package Omeka\Record\Mixin
 */
class Mixin_FileSearchTexts extends Omeka_Record_Mixin_AbstractMixin
{
    public function afterSave($args)
    {
        foreach ($this->_getFileSearchTexts() as $fileText) {
            $this->_record->addSearchText($fileText);
        }
    }

    /**
     * Rebuild and save the item's search text directly, outside a save.
     *
     * Keeps the item's search text current when an attached file changes,
     * without the side effects of a full item save (modified timestamp,
     * plugin hooks). Must mirror what accumulates during a save: the Dublin
     * Core title and element texts (Mixin_ElementText) and tag names
     * (Mixin_Tag). File texts are shared with afterSave() via
     * _getFileSearchTexts().
     *
     * @see Mixin_ParentItemSearchText
     */
    public function rebuildSearchText()
    {
        $item = $this->_record;

        $title = null;
        $text = '';

        $titles = $item->getElementTexts('Dublin Core', 'Title');
        if ($titles) {
            $title = $titles[0]->text;
        }
        foreach ($item->getSearchElementTexts() as $elementText) {
            $text .= "{$elementText->text} ";
        }
        foreach ($item->getTags() as $tag) {
            $text .= "{$tag->name} ";
        }
        foreach ($this->_getFileSearchTexts() as $fileText) {
            $text .= "$fileText ";
        }

        Mixin_Search::saveSearchText('Item', $item->id, $text, $title, $item->public);
    }

    /**
     * Get the search texts of the item's files, if file texts are configured
     * to be included in the item's search text.
     *
     * @return array Text strings.
     */
    private function _getFileSearchTexts()
    {
        if (!get_option('search_include_file_texts')) {
            return [];
        }

        $texts = [];
        foreach ($this->_record->getFiles() as $file) {
            foreach ($file->getSearchElementTexts() as $elementText) {
                $texts[] = $elementText->text;
            }
            release_object($file);
        }
        return $texts;
    }
}
