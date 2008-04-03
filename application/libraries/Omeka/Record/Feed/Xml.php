<?php 
/**
* This class knows most of the details for how to render model objects directly into XML
*/
abstract class Omeka_Record_Feed_Xml extends Omeka_Record_Feed_Abstract
{	
	protected function sanitizeXML($xml)
	{
		return '<?xml version="1.0" encoding="UTF-8"?>' . str_replace('<?xml version="1.0"?>', '', $xml);
	}
	
	public function renderOne(Omeka_Record $record)
	{
		return $this->sanitizeXML($this->recordToXml($record));
	}
	
	public function renderAll(array $records) {
		$first = current($records);
		
		//For a set of Item objects, this would be 'items'
		$root = $first->getPluralized();
		
		//Start the XML doc
		$xml = "<$root>";
		
		//Loop through the records and add the resulting XML to the big-un
		foreach ($records as $key => $record) {
			$record_xml = $this->recordToXml($record, false);
			$xml .= $record_xml;
		}
		
		//Close the XML body element
		$xml .= "</$root>";
		
		return $this->sanitizeXML($xml);
	}
	
	/**
	 * @param bool Whether or not to return the SimpleXML object itself (default is XML string)
	 *
	 * @return mixed SimpleXMLElement|string
	 **/
	public function recordToXml($record, $return_obj=false)
	{
		$root = strtolower(get_class($record));
		
		$wrapper = "<$root></$root>";
		
		$xml = new SimpleXmlElement($wrapper);
		
		$fields = $record->toArray();
		
		foreach ($fields as $field => $value) {
			$xml->$field = $value;
		}
		
		if($return_obj) return $xml;
		
		return $xml->asXML();
	}
}
 
?>
