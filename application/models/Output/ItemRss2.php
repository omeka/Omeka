<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Output
 */
class Output_ItemRss2
{
    public function render(array $records)
    {
        $entries = array();
        foreach ($records as $record) {
            $entries[] = $this->itemToRss($record);
            release_object($record);
        }
                
        $headers = $this->buildRSSHeaders();
        $headers['entries'] = $entries;
        $feed = Zend_Feed::importArray($headers, 'rss');
        return $feed->saveXML();        
    }
    
    protected function buildRSSHeaders()
    {
        $headers = array();
        
        // How do we determine what title to give the RSS feed?        
        $headers['title'] = option('site_title');
        
        $headers['link'] = xml_escape(get_view()->serverUrl(isset($_SERVER['REQUEST_URI'])));
        $headers['lastUpdate'] = time();
        $headers['charset'] = "UTF-8";    
        
        // Feed could have a description, where would it be stored ?
        // $headers['description'] = ""
        
        $headers['author'] = option('site_title');
        $headers['email'] = option('administrator_email');
        $headers['copyright'] = option('copyright');
        
        //How do we determine how long a feed can be cached?
        //$headers['ttl'] = 
        
        return $headers;
    }
    
    protected function buildDescription($item)
    {
        $description = all_element_texts($item);
        
        //Output HTML that would display all the files in whatever way is possible
        $description .= file_markup($item->Files);
        
        return $description;
    }
    
    protected function itemToRSS($item)
    {        
        $entry = array();
        set_current_record('item', $item, true);
        
        // Title is a CDATA section, so no need for extra escaping.
        $entry['title'] = strip_formatting(metadata($item, array('Dublin Core', 'Title'), array('no_escape'=>true)));
        $entry['description'] = $this->buildDescription($item);
        
        $entry['link'] = xml_escape(record_url($item, null, true));
                
        $entry['lastUpdate'] = strtotime($item->added);
                
        //List the first file as an enclosure (only one per RSS feed)
        if(($files = $item->Files) && ($file = current($files))) {
            $entry['enclosure']   = array();
            $fileDownloadUrl = file_display_url($file);
            $enc['url']           = $fileDownloadUrl;
            $enc['type']          = $file->mime_type;
            $enc['length']        = (int) $file->size;
            $entry['enclosure'][] = $enc;
        }

        return $entry;        
    }
}
