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
 */
class ItemAtom
{
    /**
     * @var DOMDocument
     */
    private $_feed;
    
    /**
     * Build the Atom feed using DOM.
     * 
     * @param array $items An array of Item records.
     * @return void
     */
    public function __construct(array $items)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        
        // feed
        $feedElement = $doc->createElement('feed');
        $feedElement->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        
        // feed/id
        $feedIdElement = $doc->createElement('id');
        $feedIdElement->appendChild($doc->createTextNode(__v()->serverUrl(isset($_SERVER['REQUEST_URI']))));
        $feedElement->appendChild($feedIdElement);
        
        // feed/title
        $feedTitleElement = $doc->createElement('title');
        $feedTitleElement->appendChild($doc->createCDATASection(get_option('site_title')));
        $feedElement->appendChild($feedTitleElement);
        
        // feed/subtitle
        $feedSubtitleElement = $doc->createElement('subtitle');
        $feedSubtitleElement->appendChild($doc->createCDATASection(get_option('description')));
        $feedElement->appendChild($feedSubtitleElement);
        
        // feed/author/name
        $feedAuthor = $doc->createElement('author');
        $feedAuthorName = $doc->createElement('name');
        $feedAuthorName->appendChild($doc->createCDATASection(get_option('author')));
        $feedAuthor->appendChild($feedAuthorName);
        $feedElement->appendChild($feedAuthor);
        
        // feed/rights
        $feedRightsElement = $doc->createElement('rights');
        $feedRightsElement->appendChild($doc->createCDATASection(get_option('copyright')));
        $feedElement->appendChild($feedRightsElement);
        
        // feed/updated
        $feedUpdated = $doc->createElement('updated', date(DATE_ATOM, time()));
        $feedElement->appendChild($feedUpdated);
        
        // feed/generator
        $feedGenerator = $doc->createElement('generator', 'Omeka');
        $feedElement->appendChild($feedGenerator);
        
        // feed/link[rel=self]
        $feedLinkSelfElement = $doc->createElement('link');
        $feedLinkSelfElement->setAttribute('rel', 'self');
        $feedLinkSelfElement->setAttribute('href', __v()->serverUrl(isset($_SERVER['REQUEST_URI'])));
        $feedElement->appendChild($feedLinkSelfElement);
        
        // feed/link[rel=prev]
        // [...]
        
        // feed/link[rel=next]
        // [...]
        
        // feed/entry
        foreach ($items as $item) {
            $feedEntryElement = $doc->createElement('entry');
            
            // feed/entry/id
            $feedEntryIdElement = $doc->createElement('id');
            $feedEntryIdElement->appendChild($doc->createTextNode(abs_item_uri($item)));
            $feedEntryElement->appendChild($feedEntryIdElement);
            
            // feed/entry/title
            $feedEntryTitleElement = $doc->createElement('title');
            $feedEntryTitleElement->appendChild($doc->createCDATASection(item('Dublin Core', 'Title', null, $item)));
            $feedEntryElement->appendChild($feedEntryTitleElement);
            
            // feed/entry/summary
            $feedEntrySummaryElement = $doc->createElement('summary');
            $feedEntrySummaryElement->appendChild($doc->createCDATASection(item('Dublin Core', 'Description', null, $item)));
            $feedEntryElement->appendChild($feedEntrySummaryElement);
            
            // feed/entry/updated
            $feedEntryUpdated = $doc->createElement('updated', date(DATE_ATOM, strtotime(item('Date Modified', null, null, $item))));
            $feedEntryElement->appendChild($feedEntryUpdated);
            
            // feed/entry/link[rel=alternate]
            $feedEntryLinkAlternateElement = $doc->createElement('link');
            $feedEntryLinkAlternateElement->setAttribute('rel', 'alternate');
            $feedEntryLinkAlternateElement->setAttribute('href', abs_item_uri($item));
            $feedEntryElement->appendChild($feedEntryLinkAlternateElement);
            
            // feed/entry/link[rel=enclosure]
            foreach ($item->Files as $file) {
                $feedEntryLinkEnclosureElement = $doc->createElement('link');
                $feedEntryLinkEnclosureElement->setAttribute('rel', 'enclosure');
                $feedEntryLinkEnclosureElement->setAttribute('href', $file->getWebPath());
                $feedEntryLinkEnclosureElement->setAttribute('type', $file->mime_browser);
                $feedEntryLinkEnclosureElement->setAttribute('length', $file->size);
                $feedEntryElement->appendChild($feedEntryLinkEnclosureElement);
            }
            
            // feed/entry/category
            foreach ($item->Tags as $tag) {
                $feedEntryCategoryElement = $doc->createElement('category');
                $feedEntryCategoryElement->setAttribute('term', $tag->name);
                $feedEntryElement->appendChild($feedEntryCategoryElement);
            }
            
            // feed/entry/content
            $feedEntryContentElement = $doc->createElement('content');
            $feedEntryContentElement->appendChild($doc->createCDATASection(show_item_metadata(array(), $item)));
            $feedEntryElement->appendChild($feedEntryContentElement);
            
            $feedElement->appendChild($feedEntryElement);
        }
        
        $doc->appendChild($feedElement);
        
        $this->_feed = $doc;
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