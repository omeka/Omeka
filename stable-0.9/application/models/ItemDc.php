<?php 
/**
* 
*/
class ItemDc extends Omeka_Record_Feed_Dc
{
    // Set the Dublin Core Metadata Element Set (DCMES).
    public $dcmes = array('contributor', 'coverage', 'creator', 'date', 
                          'description', 'format', 'identifier', 'language', 
                          'publisher', 'relation', 'rights', 'source', 
                          'subject', 'title', 'type');

    public function recordToDc($item)
    {
        require_once HELPERS;
        
        $xml .= '
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
 
?>
