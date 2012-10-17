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
class Output_ItemDcmesXml
{
    private $_dcElements = array('Title', 'Subject', 'Description', 
                                 'Creator', 'Source', 'Publisher', 
                                 'Date', 'Contributor', 'Rights', 
                                 'Relation', 'Format', 'Language', 
                                 'Type', 'Identifier', 'Coverage');

    public function recordToDcmesXml($item)
    {
        $xml = "\n" . '<rdf:Description rdf:about="' . xml_escape(record_url($item, null, true)) . '">';
        // Iterate throught the DCMES.
        foreach ($this->_dcElements as $elementName) {
            if ($text = metadata($item, array('Dublin Core', $elementName), array('all'=>true, 'no_escape'=>true))) {
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
