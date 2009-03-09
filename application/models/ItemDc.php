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
class ItemDc
{
    public function recordToDc($item)
    {      
        $dcElements = $item->getElementsBySetName('Dublin Core');
    
        $xml = "\n" . '<rdf:Description rdf:about="' . abs_item_uri($item) . '">';
        // Iterate throught the DCMES.
        foreach ($dcElements as $element) {
            $elementName = $element->name;
            if ($text = item('Dublin Core', $elementName, 'all')) {
                foreach ($text as $k => $v) {
                    if (!empty($v)) {
                        $xml .= "\n" . '<dc:' . strtolower($elementName) . '><![CDATA[' 
                            . $v . ']]></dc:' . strtolower($elementName) . '>';
                    }
                }
            }
        }
        $xml .= "\n" . '</rdf:Description>';
        return $xml;        
    }
}