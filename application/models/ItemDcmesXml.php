<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
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
