<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Model class for an Atom feed for a list of items.
 * 
 * @package Omeka\Output
 */
class Output_ItemAtom
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
        $feedIdElement->appendChild($doc->createTextNode(get_view()->serverUrl(isset($_SERVER['REQUEST_URI']))));
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
            $feedEntryIdElement->appendChild($doc->createTextNode(record_url($item, null, true)));
            $feedEntryElement->appendChild($feedEntryIdElement);
            
            // feed/entry/title
            if (!$feedEntryTitle = metadata($item, array('Dublin Core', 'Title'))) {
                $feedEntryTitle = 'Untitled';
            }
            $feedEntryTitleElement = $doc->createElement('title');
            $feedEntryTitleElement->appendChild($doc->createCDATASection($feedEntryTitle));
            $feedEntryElement->appendChild($feedEntryTitleElement);
            
            // feed/entry/summary
            if ($feedEntrySummary = metadata($item, array('Dublin Core', 'Description'))) {
                $feedEntrySummaryElement = $doc->createElement('summary');
                $feedEntrySummaryElement->appendChild($doc->createCDATASection($feedEntrySummary));
                $feedEntryElement->appendChild($feedEntrySummaryElement);
            }
            
            // feed/entry/updated
            $feedEntryUpdated = $doc->createElement('updated', date(DATE_ATOM, strtotime(metadata($item, 'Modified'))));
            $feedEntryElement->appendChild($feedEntryUpdated);
            
            // feed/entry/link[rel=alternate]
            $feedEntryLinkAlternateElement = $doc->createElement('link');
            $feedEntryLinkAlternateElement->setAttribute('rel', 'alternate');
            $feedEntryLinkAlternateElement->setAttribute('type', 'text/html');
            $feedEntryLinkAlternateElement->setAttribute('href', record_url($item, null, true));
            $feedEntryElement->appendChild($feedEntryLinkAlternateElement);
            
            // feed/entry/link[rel=enclosure]
            foreach ($item->Files as $file) {
                $feedEntryLinkEnclosureElement = $doc->createElement('link');
                $feedEntryLinkEnclosureElement->setAttribute('rel', 'enclosure');
                $feedEntryLinkEnclosureElement->setAttribute('href', $file->getWebPath('original'));
                $feedEntryLinkEnclosureElement->setAttribute('type', $file->mime_type);
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
            $feedEntryContentElement->appendChild($doc->createCDATASection(all_element_texts($item)));
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
        $feedLinks['self'] = get_view()->serverUrl(isset($_SERVER['REQUEST_URI']));
        
        // If pagination is registered, assume item browse or search.
        if (Zend_Registry::isRegistered('pagination')) {
            $pagination = Zend_Registry::get('pagination');
            
            // Set the pagination information.
            $paginator = Zend_Paginator::factory($pagination['total_results']);
            $paginator->setCurrentPageNumber($pagination['page']);
            $paginator->setItemCountPerPage($pagination['per_page']);
            $pages = $paginator->getPages();
            
            // first
            $feedLinks['first'] = absolute_url(array('page' => $pages->first), 'default', $_GET);
            
            // previous
            if (isset($pages->previous)) {
                $feedLinks['previous'] = absolute_url(array('page' => $pages->previous), 'default', $_GET);
            }
            
            // next
            if (isset($pages->next)) {
                $feedLinks['next'] = absolute_url(array('page' => $pages->next), 'default', $_GET);
            }
            
            // last
            $feedLinks['last'] = absolute_url(array('page' => $pages->last), 'default', $_GET);
            
        // If pagination is not registered, assume item show.
        } else {
            
            // first
            if ($itemFirst = get_db()->getTable('Item')->findFirst()) {
                $feedLinks['first'] = record_url($itemFirst, null, true) . '?output=atom';
            }
            
            // previous
            if ($itemPrev = $items[0]->previous()) {
                $feedLinks['previous'] = record_url($itemPrev, null, true) . '?output=atom';
            }
            
            // next
            if ($itemNext = $items[0]->next()) {
                $feedLinks['next'] = record_url($itemNext, null, true) . '?output=atom';
            }
            
            // last
            if ($itemLast = get_db()->getTable('Item')->findLast()) {
                $feedLinks['last'] = record_url($itemLast, null, true) . '?output=atom';
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
