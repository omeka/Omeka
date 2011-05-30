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
        $doc->formatOutput = true;
        
        // feed
        $feedElement = $doc->createElement('feed');
        $feedElement->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        
        // feed/id
        $feedIdElement = $doc->createElement('id');
        $feedIdElement->appendChild($doc->createTextNode(__v()->serverUrl(isset($_SERVER['REQUEST_URI']))));
        $feedElement->appendChild($feedIdElement);
        
        // feed/title
        if (!$feedTitle = get_option('site_title')) {
            $feedTitle = 'Unknown';
        }
        $feedTitleElement = $doc->createElement('title');
        $feedTitleElement->appendChild($doc->createCDATASection($feedTitle));
        $feedElement->appendChild($feedTitleElement);
        
        // feed/subtitle
        if ($feedSubtitle = get_option('description')) {
            $feedSubtitleElement = $doc->createElement('subtitle');
            $feedSubtitleElement->appendChild($doc->createCDATASection($feedSubtitle));
            $feedElement->appendChild($feedSubtitleElement);
        }
        
        // feed/author/name
        if (!$feedAuthorName = get_option('author')) {
            $feedAuthorName = 'Unknown';
        }
        $feedAuthorElement = $doc->createElement('author');
        $feedAuthorNameElement = $doc->createElement('name');
        $feedAuthorNameElement->appendChild($doc->createCDATASection($feedAuthorName));
        $feedAuthorElement->appendChild($feedAuthorNameElement);
        $feedElement->appendChild($feedAuthorElement);
        
        // feed/rights
        if ($feedRights = get_option('copyright')) {
            $feedRightsElement = $doc->createElement('rights');
            $feedRightsElement->appendChild($doc->createCDATASection($feedRights));
            $feedElement->appendChild($feedRightsElement);
        }
        
        // feed/updated
        $feedUpdated = $doc->createElement('updated', date(DATE_ATOM, time()));
        $feedElement->appendChild($feedUpdated);
        
        // feed/generator
        $feedGenerator = $doc->createElement('generator', 'Omeka');
        $feedElement->appendChild($feedGenerator);
        
        // Get the feed self/next/prev links.
        $feedLinks = $this->_getFeedLinks($items);
        
        // feed/link[rel=self]
        $feedLinkSelfElement = $doc->createElement('link');
        $feedLinkSelfElement->setAttribute('rel', 'self');
        $feedLinkSelfElement->setAttribute('href', $feedLinks['self']);
        $feedElement->appendChild($feedLinkSelfElement);
        
        // feed/link[rel=first]
        $feedLinkFirstElement = $doc->createElement('link');
        $feedLinkFirstElement->setAttribute('rel', 'first');
        $feedLinkFirstElement->setAttribute('type', 'application/atom+xml');
        $feedLinkFirstElement->setAttribute('href', $feedLinks['first']);
        $feedElement->appendChild($feedLinkFirstElement);
        
        // feed/link[rel=prev]
        if (isset($feedLinks['previous'])) {
            $feedLinkPrevElement = $doc->createElement('link');
            $feedLinkPrevElement->setAttribute('rel', 'previous');
            $feedLinkPrevElement->setAttribute('type', 'application/atom+xml');
            $feedLinkPrevElement->setAttribute('href', $feedLinks['previous']);
            $feedElement->appendChild($feedLinkPrevElement);
        }
        
        // feed/link[rel=next]
        if (isset($feedLinks['next'])) {
            $feedLinkNextElement = $doc->createElement('link');
            $feedLinkNextElement->setAttribute('rel', 'next');
            $feedLinkNextElement->setAttribute('type', 'application/atom+xml');
            $feedLinkNextElement->setAttribute('href', $feedLinks['next']);
            $feedElement->appendChild($feedLinkNextElement);
        }
        
        // feed/link[rel=last]
        $feedLinkLastElement = $doc->createElement('link');
        $feedLinkLastElement->setAttribute('rel', 'last');
        $feedLinkLastElement->setAttribute('type', 'application/atom+xml');
        $feedLinkLastElement->setAttribute('href', $feedLinks['last']);
        $feedElement->appendChild($feedLinkLastElement);
        
        // feed/entry
        foreach ($items as $item) {
            $feedEntryElement = $doc->createElement('entry');
            
            // feed/entry/id
            $feedEntryIdElement = $doc->createElement('id');
            $feedEntryIdElement->appendChild($doc->createTextNode(abs_item_uri($item)));
            $feedEntryElement->appendChild($feedEntryIdElement);
            
            // feed/entry/title
            if (!$feedEntryTitle = item('Dublin Core', 'Title', null, $item)) {
                $feedEntryTitle = 'Untitled';
            }
            $feedEntryTitleElement = $doc->createElement('title');
            $feedEntryTitleElement->appendChild($doc->createCDATASection($feedEntryTitle));
            $feedEntryElement->appendChild($feedEntryTitleElement);
            
            // feed/entry/summary
            if ($feedEntrySummary = item('Dublin Core', 'Description', null, $item)) {
                $feedEntrySummaryElement = $doc->createElement('summary');
                $feedEntrySummaryElement->appendChild($doc->createCDATASection($feedEntrySummary));
                $feedEntryElement->appendChild($feedEntrySummaryElement);
            }
            
            // feed/entry/updated
            $feedEntryUpdated = $doc->createElement('updated', date(DATE_ATOM, strtotime(item('Date Modified', null, null, $item))));
            $feedEntryElement->appendChild($feedEntryUpdated);
            
            // feed/entry/link[rel=alternate]
            $feedEntryLinkAlternateElement = $doc->createElement('link');
            $feedEntryLinkAlternateElement->setAttribute('rel', 'alternate');
            $feedEntryLinkAlternateElement->setAttribute('type', 'text/html');
            $feedEntryLinkAlternateElement->setAttribute('href', abs_item_uri($item));
            $feedEntryElement->appendChild($feedEntryLinkAlternateElement);
            
            // feed/entry/link[rel=enclosure]
            foreach ($item->Files as $file) {
                $feedEntryLinkEnclosureElement = $doc->createElement('link');
                $feedEntryLinkEnclosureElement->setAttribute('rel', 'enclosure');
                $feedEntryLinkEnclosureElement->setAttribute('href', $file->getWebPath('archive'));
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
            $feedEntryContentElement->setAttribute('type', 'html');
            $feedEntryContentElement->appendChild($doc->createCDATASection(show_item_metadata(array(), $item)));
            $feedEntryElement->appendChild($feedEntryContentElement);
            
            $feedElement->appendChild($feedEntryElement);
        }
        
        $doc->appendChild($feedElement);
        
        $this->_feed = $doc;
    }
    
    /**
     * Returns the URLs, if any, for rel=self|next|previous links.
     * 
     * @param array $items
     * @return array
     */
    private function _getFeedLinks(array $items)
    {
        $feedLinks = array();
        
        // There is always a self link.
        $feedLinks['self'] = __v()->serverUrl(isset($_SERVER['REQUEST_URI']));
        
        // If pagination is registered, assume item browse or search.
        if (Zend_Registry::isRegistered('pagination')) {
            $pagination = Zend_Registry::get('pagination');
            
            // Set the pagination information.
            $paginator = Zend_Paginator::factory($pagination['total_results']);
            $paginator->setCurrentPageNumber($pagination['page']);
            $paginator->setItemCountPerPage($pagination['per_page']);
            $pages = $paginator->getPages();
            
            // first
            $feedLinks['first'] = abs_uri(array('page' => $pages->first), 'default', $_GET);
            
            // previous
            if (isset($pages->previous)) {
                $feedLinks['previous'] = abs_uri(array('page' => $pages->previous), 'default', $_GET);
            }
            
            // next
            if (isset($pages->next)) {
                $feedLinks['next'] = abs_uri(array('page' => $pages->next), 'default', $_GET);
            }
            
            // last
            $feedLinks['last'] = abs_uri(array('page' => $pages->last), 'default', $_GET);
            
        // If pagination is not registered, assume item show.
        } else {
            
            // first
            if ($itemFirst = get_db()->getTable('Item')->findFirst()) {
                $feedLinks['first'] = abs_item_uri($itemFirst) . '?output=atom';
            }
            
            // previous
            if ($itemPrev = $items[0]->previous()) {
                $feedLinks['previous'] = abs_item_uri($itemPrev) . '?output=atom';
            }
            
            // next
            if ($itemNext = $items[0]->next()) {
                $feedLinks['next'] = abs_item_uri($itemNext) . '?output=atom';
            }
            
            // last
            if ($itemFirst = get_db()->getTable('Item')->findLast()) {
                $feedLinks['last'] = abs_item_uri($itemFirst) . '?output=atom';
            }
        }
        
        return $feedLinks;
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
