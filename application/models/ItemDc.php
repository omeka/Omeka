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
    // Set the Dublin Core Metadata Element Set (DCMES).
    public $dcmes = array('contributor', 'coverage', 'creator', 'date', 
                          'description', 'format', 'identifier', 'language', 
                          'publisher', 'relation', 'rights', 'source', 
                          'subject', 'title', 'type');

    public function recordToDc($item)
    {        
        $xml = '
    <rdf:Description rdf:about="' . item_permalink_url($item) . '">';
        // Iterate throught the DCMES.
        foreach ($this->dcmes as $element) {
            if (isset($item->$element) && strlen($item->$element)) {
                $xml .= '
        <dc:' . $element . '>' . htmlspecialchars($item->$element) . '</dc:' . $element . '>';
            }
        }
        $xml .= '
    </rdf:Description>';
        
        return $xml;        
    }
}