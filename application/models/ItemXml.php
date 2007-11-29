<?php 
/**
* 
*/
class ItemXml extends Omeka_Record_Feed_Xml
{
	public function recordToXml($item)
	{
		//Get a SimpleXML object
		$xml = parent::recordToXml($item, true);
		
		$type_metadata = $xml->addChild('type_metadata');
		
		//Append type metadata to the XML file
		foreach ($item->TypeMetadata as $name => $value) {
			$field_entry = $type_metadata->addChild('field', $value);
			$field_entry->addAttribute('name', $name);
		}
		
		//Append the name of the collection
		if($this->Collection) {
			$xml->collection = $item->Collection->name;
		}
		
		//Append the name of the Type
		if($this->type_id) {
			$xml->type = $item->Type->name;
		}
		
		//Append the files and their titles
		if($this->Files) {			
			
			$files = $xml->addChild('files');
			foreach ($item->Files as $key => $file) {
				$file_entry = $files->addChild('file');
				$file_entry->title = $file->title;
			}
		}
		
		if($return_obj) return $xml;
		
		return $xml->asXML();		
	}
}
 
?>
