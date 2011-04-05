<?php
/**
 * @package Omeka
 * @subpackage Models
 * @copyright Copyright (c) 2011 Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Model class for an Atom feed for a list of items.
 *
 * @package Omeka
 * @subpackage Models
 * @link http://en.wikipedia.org/wiki/Atom_(standard)
 * @link http://tools.ietf.org/html/rfc4287
 * @link http://framework.zend.com/manual/en/zend.feed.importing.html
 */
class ItemAtom
{
    /**
     * @var array An array of Item records.
     */
    private $_items;
    
    /**
     * @var array A Zend_Feed formatted array.
     */
    private $_atom = array();

    /**
     * @var Zend_Feed_Atom The Zend_Feed instance for Atom.
     */
    private $_feed;
    
    /**
     * Build the Atom array and set the Zend_Feed_Atom instance.
     * 
     * @param array $items An array of Item records.
     * @return void
     */
    public function __construct(array $items)
    {
        $this->_items = $items;
        $this->_buildAtom();
        $this->_feed = Zend_Feed::importArray($this->_atom, 'atom');
    }
    
    /**
     * Builds atom:feed.
     * 
     * @return void
     */
    private function _buildAtom()
    {
        $this->_atom['charset'] = 'UTF-8';
        $this->_atom['title'] = strip_tags(get_option('site_title'));
        $this->_atom['link'] = xml_escape(__v()->serverUrl(isset($_SERVER['REQUEST_URI'])));
        $this->_atom['author'] = strip_tags(get_option('author'));
        $this->_atom['description'] = strip_tags(get_option('description'));
        $this->_atom['copyright'] = strip_tags(get_option('copyright'));
        $this->_atom['entries'] = $this->_buildEntries();
    }
    
    /**
     * Builds atom:entry.
     * 
     * @return array
     */
    private function _buildEntries()
    {
        $entries = array();

        foreach ($this->_items as $item) {
            $entries[] = array('title' => strip_tags(item('Dublin Core', 'Title', array('no_escape'=>true), $item)),
                               'link' => abs_item_uri($item), 
                               'description' => strip_tags(item('Dublin Core', 'Description', array('no_escape'=>true), $item)),
                               'content' => show_item_metadata(array(), $item),
                               'lastUpdate' => strtotime(item('Date Modified', null, array(), $item)),
                               'category' => $this->_buildCategories($item), 
                               'enclosure' => $this->_buildEnclosures($item));
        }
        return $entries;
    }
    
    /**
     * Builds atom:category.
     * 
     * @param Item $item An Item record.
     * @return array
     */
    private function _buildCategories(Item $item)
    {
        $categories = array();
        foreach ($item->Tags as $tag) {
            $categories[] = array('term' => $tag->name ? $tag->name : '0 ');
        }
        return $categories;
    }
    
    /**
     * Builds atom:enclosure.
     * 
     * @param Item $item An Item record.
     * @return array
     */
    private function _buildEnclosures(Item $item)
    {
        $enclosures = array();
        foreach ($item->Files as $file) {
            $enclosures[] = array('url' => $file->getWebPath(),
                                  'type' => $file->mime_browser, 
                                  'length' => $file->size);
        }
        return $enclosures;
    }
    
    /**
     * Returns the XML feed.
     * 
     * @return string
     */
    public function getFeed()
    {
        return $this->_feed->saveXML();
    }
}