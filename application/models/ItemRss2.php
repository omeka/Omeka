<?php 
/**
* 
*/
class ItemRss2 extends Omeka_Record_Feed_Abstract
{
	public function renderAll(array $records)
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
	
	public function renderOne(Omeka_Record $item)
	{
		throw new Exception( 'Cannot render an RSS feed for a single item!' );
	}

	protected function buildRSSHeaders()
	{
		require_once HELPERS;
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
	
	protected function itemToRSS($item)
	{
		$entry = array();
		
		$entry['title'] = $item->title;
		$entry['description'] = $item->description;
		
		//Need to use the link helpers for this one
		require_once HELPERS;
		
		//Permalink (this is kind of duplicated elsewhere)
		$entry['link'] = item_permalink_url($item);
				
		$entry['lastUpdate'] = strtotime($item->added);
		
		//Branch on type to figure out what to put in the 'content' part
		switch ($item->Type->name) {
			case 'Document':
				$entry['content'] = $item->getMetatext('Text');
				break;
			
			case 'Still Image':
				
				//Append the HTML for the image to the 'description' of the rss entry
				ob_start();
				fullsize($item);
				$fullsize_html = ob_get_clean();
				$entry['description'] .= $fullsize_html;
	
				//List the file as an enclosure (whatever that means)
				if($item->Files and ($file = current($item->Files))) {
					$entry['enclosure'] = array();
					$enc['url'] = file_display_uri($file);
					$enc['type'] = $file->mime_browser;
					$enc['length'] = (int) $file->size;
					$entry['enclosure'][] = $enc;
				}
				break;
				
			default:
				//Do nothing
				break;
		}
				
//		if($this->type_id) $entry['category'] = $this->Type->name;
		
		return $entry;		
	}
}
 
?>
