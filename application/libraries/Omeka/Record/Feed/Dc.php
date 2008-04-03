<?php 
/**
* This class knows most of the details for how to render model objects directly into XML
*/
abstract class Omeka_Record_Feed_Dc extends Omeka_Record_Feed_Abstract
{        
    public function renderOne(Omeka_Record $record)
    {
        $records = array($record);
        return $this->renderAll($records);
    }
    
    public function renderAll(array $records) {
        $xml .= '<?xml version="1.0"?>
<!DOCTYPE rdf:RDF PUBLIC "-//DUBLIN CORE//DCMES DTD 2002/07/31//EN"
    "http://dublincore.org/documents/2002/07/31/dcmes-xml/dcmes-xml-dtd.dtd">
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/">';
        // Iterate through the items.
        foreach ($records as $item) {
            $xml .= $this->recordToDc($item);        
        }
        $xml .= '
</rdf:RDF>';

        return $xml;
    }
    
}
 
?>
