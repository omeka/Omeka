<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Can convert one or more Omeka_Record objects into an XML representation.
 *
 * @todo There is no namespace for this XML and thus no standard (as of now),
 * so this default XML representation has no value to the standards-oriented
 * museum community.  We should either deprecate this XML format or refine
 * and namespace it.  
 * @see ItemXml.php
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Record_Feed_Xml
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
		$root = strtolower(Inflector::pluralize(get_class($first)));
		
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