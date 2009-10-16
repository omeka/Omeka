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
class ItemDcmesXml
{
    private $_dcElements = array('Title', 'Subject', 'Description', 
                                 'Creator', 'Source', 'Publisher', 
                                 'Date', 'Contributor', 'Rights', 
                                 'Relation', 'Format', 'Language', 
                                 'Type', 'Identifier', 'Coverage');

    public function recordToDcmesXml($item)
    {
        $xml = "\n" . '<rdf:Description rdf:about="' . xml_escape(abs_item_uri($item)) . '">';
        // Iterate throught the DCMES.
        foreach ($this->_dcElements as $elementName) {
            if ($text = item('Dublin Core', $elementName, array('all'=>true, 'no_escape'=>true))) {
                foreach ($text as $k => $v) {
                    if (!empty($v)) {
                        $xml .= "\n" . '<dc:' . strtolower($elementName) . '>' 
                            . xml_escape($v) . '</dc:' . strtolower($elementName) . '>';
                    }
                }
            }
        }
        $xml .= "\n" . '</rdf:Description>';
        return $xml;        
    }
}