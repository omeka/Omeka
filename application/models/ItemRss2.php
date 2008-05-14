<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemRss2
{
	public function render(array $records)
	{
		$entries = array();
		foreach ($records as $record) {
			$entries[] = $this->itemToRss($record);
		}
				
		$headers = $this->buildRSSHeaders();
		
		$headers['entries'] = $entries;
		
		$feed = Zend_Feed::importArray($headers, 'rss');
		
		return $feed->saveXML();		
	}

	protected function buildRSSHeaders()
	{
		$headers = array();

//		How do we determine what title to give the RSS feed?		
		$headers['title'] = get_option('site_title');
		
		$headers['link'] = h('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$headers['lastUpdate'] = time();
		$headers['charset'] = "UTF-8";	

//		Feed could have a description, where would it be stored ?
//		$headers['description'] = ""

		$headers['author'] = get_option('site_title');
		$headers['email'] = get_option('administrator_email');
		$headers['copyright'] = get_option('copyright');
		
		//How do we determine how long a feed can be cached?
		//$headers['ttl'] = 
		
		return $headers;
	}
	
	protected function buildDescription($item)
	{
	    $description = '';
	    
	    //Output a list of the dublin core fields that have values
	    $coreFields = Item::fields(false);
	    foreach ($coreFields as $field => $readableField) {
	        $text = $item->$field;
	        if(!empty($text)) {
	            $description .= nls2p("<strong>" . $readableField. "</strong>: " . h($text));
	        }
	    }
   
        //Item Type data
        
        if($type = $item->Type) {
            $description .= "<p><strong>Item Type</strong>: {$type->name}</p>";
        }
        
	    //Output all the metafields for the item
	
	    foreach ($item->TypeMetadata as $field => $text) {
	       $description .= nls2p("<strong>" . $field . "</strong>: " . h($text));
	    }
	 
	    //Output HTML that would display all the files in whatever way is possible
	    $description .= display_files($item->Files);
	    
	    return $description;
	}
	
	protected function itemToRSS($item)
	{	    
		$entry = array();
		
		$entry['title'] = $item->title;
		$entry['description'] = $this->buildDescription($item);
		
		//Permalink (this is kind of duplicated elsewhere)
		$entry['link'] = item_permalink_url($item);
				
		$entry['lastUpdate'] = strtotime($item->added);
		
		//List the first file as an enclosure (only one per RSS feed)
		if($item->Files and ($file = current($item->Files))) {
			$entry['enclosure'] = array();
			$enc['url'] = file_display_uri($file);
			$enc['type'] = $file->mime_browser;
			$enc['length'] = (int) $file->size;
			$entry['enclosure'][] = $enc;
		}
		
		return $entry;		
	}
}