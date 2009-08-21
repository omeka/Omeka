<?php
// think about wrapping everything in an rdf, like output=dcmes-xml
// think about some way to include the URL
abstract class Omeka_Output_Xml
{
    const XMLNS_XSI = 'http://www.w3.org/2001/XMLSchema-instance';
    const XMLNS = 'http://www.omeka.org/schema/omeka-xml';
    const XMLNS_SCHEMALOCATION = 'http://www.omeka.org/schema/omeka-xml/2009-08-18/omeka-xml.xsd';
    
    protected $_record;
    protected $_doc;
    
    abstract protected function _buildDoc();
    
    public function __construct($record)
    {
        $this->_record = $record;
        $this->_doc = new DOMDocument('1.0', 'UTF-8');
        $this->_doc->formatOutput = true;
        $this->_buildDoc();
    }
    
    public function getDoc()
    {
        return $this->_doc;
    }
    
    protected function _createRootElement($name)
    {
        $rootElement = $this->_doc->createElementNS(self::XMLNS, $name);
        $rootElement->setAttribute('xmlns:xsi', self::XMLNS_XSI);
        $rootElement->setAttribute('xsi:schemaLocation', self::XMLNS_SCHEMALOCATION);
        $rootElement->setAttribute("{$name}Id", $this->_record->id);
        return $rootElement;
    }
    
    protected function _createElement($name, $value = null, $id = null, $parentElement = null)
    {
        $element = $this->_doc->createElement($name);
        
        // Append the value, if given.
        if ($value) {
            $textNode = $this->_doc->createTextNode($value);
            $element->appendChild($textNode);
        }
        
        // Set the @id attribute, if given.
        if ($id) {
            $element->setAttribute("{$name}Id", $id);
        }
        
        // Append to the parent element, if given.
        if ($parentElement) {
            $parentElement->appendChild($element);
        }
        
        return $element;
    }
    
    /**
     * Get all element sets, elements, and element texts associated with the 
     * provided record.
     * 
     * @param Omeka_Record $record The record from which to extract metadata.
     * @param bool $getItemType Whether to get the item type metadata.
     * @return stdClass A list of element sets or an item type.
     */
    protected function _getElemetSetsByElementTexts(Omeka_Record $record, $getItemType = false)
    {
        $elementSets = new stdClass;
        $itemType    = new stdClass;
        
        // Get all element texts associated with the provided record.
        $elementTexts = $record->getElementTextRecords();
        foreach ($elementTexts as $elementText) {
            
            // Get associated element and element set records.
            $element    = get_db()->getTable('Element')->find($elementText->element_id);
            $elementSet = get_db()->getTable('ElementSet')->find($element->element_set_id);
            
            // Differenciate between the element sets and the "Item Type 
            // Metadata" pseudo element set.
            if (ELEMENT_SET_ITEM_TYPE == $elementSet->name) {
                $itemType->elements[$element->id]->name = $element->name;
                $itemType->elements[$element->id]->description = $element->description;
                $itemType->elements[$element->id]->elementTexts[$elementText->id]->text = $elementText->text;
            } else {
                $elementSets->elementSets[$elementSet->id]->name = $elementSet->name;
                $elementSets->elementSets[$elementSet->id]->description = $elementSet->description;
                $elementSets->elementSets[$elementSet->id]->elements[$element->id]->name = $element->name;
                $elementSets->elementSets[$elementSet->id]->elements[$element->id]->description = $element->description;
                $elementSets->elementSets[$elementSet->id]->elements[$element->id]->elementTexts[$elementText->id]->text = $elementText->text;
            }
        }
        
        // Return the item type metadata.
        if ($getItemType) {
            $itemType->id          = $record->Type->id;
            $itemType->name        = $record->Type->name;
            $itemType->description = $record->Type->description;
            return $itemType;
        }
        
        // Return the element sets metadata.
        return $elementSets;
    }
    
    /**
     * Build an elementSetContainer element, in a record (Item or File) context.
     * 
     * @param Omeka_Record $record The record from which to build element sets.
     * @param DOMElement|null $parentElement The element set container will 
     *        append to this parent element.
     * @return void|null
     */
    protected function _buildElementSetContainerForRecord(Omeka_Record $record, DOMElement $parentElement)
    {
        $elementSets = $this->_getElemetSetsByElementTexts($record);
        
        // Return if there are no element sets.
        if (!count($elementSets->elementSets)) {
            return null;
        }
        
        foreach ($elementSets->elementSets as $elementSetId => $elementSet) {
            // elementSetContainer
            $elementSetContainerElement = $this->_createElement('elementSetContainer');
            // elementSet
            $elementSetElement = $this->_createElement('elementSet', null, $elementSetId);
            $nameElement = $this->_createElement('name', $elementSet->name, null, $elementSetElement);
            $descriptionElement = $this->_createElement('description', $elementSet->description, null, $elementSetElement);
            // elementContainer
            $elementContainerElement = $this->_createElement('elementContainer');
            foreach ($elementSet->elements as $elementId => $element) {
                // element
                $elementElement = $this->_createElement('element', null, $elementId);
                $nameElement = $this->_createElement('name', $element->name, null, $elementElement);
                $descriptionElement = $this->_createElement('description', $element->description, null, $elementElement);
                // elementTextContainer
                $elementTextContainerElement = $this->_createElement('elementTextContainer');
                foreach ($element->elementTexts as $elementTextId => $elementText) {
                    // elementText
                    $elementTextElement = $this->_createElement('elementText', null, $elementTextId);
                    $textElement = $this->_createElement('text', $elementText->text, null, $elementTextElement);
                    $elementTextContainerElement->appendChild($elementTextElement);
                }
                $elementElement->appendChild($elementTextContainerElement);
                $elementContainerElement->appendChild($elementElement);
            }
            $elementSetElement->appendChild($elementContainerElement);
            $elementSetContainerElement->appendChild($elementSetElement);
            $parentElement->appendChild($elementSetContainerElement);
        }
    }
    
    /**
     * Build an itemType element, in an Item context.
     * 
     * @param Item $item The item from which to build the item type.
     * @param DOMElement|null $parentElement The item type will append to this 
     *        parent element.
     * @return void|null
     */
    protected function _buildItemTypeForItem(Item $item, DOMElement $parentElement)
    {
        // Return if the item does not have an item type.
        if (!$item->Type) {
            return null;
        }
        
        $itemType = $this->_getElemetSetsByElementTexts($item, true);
        
        // itemType
        $itemTypeElement = $this->_createElement('itemType', null, $itemType->id);
        $nameElement = $this->_createElement('name', $itemType->name, null, $itemTypeElement);
        $descriptionElement = $this->_createElement('description', $itemType->description, null, $itemTypeElement);
        // elementContainer
        $elementContainerElement = $this->_createElement('elementContainer');
        foreach ($itemType->elements as $elementId => $element) {
            // element
            $elementElement = $this->_createElement('element', null, $elementId);
            $nameElement = $this->_createElement('name', $element->name, null, $elementElement);
            $descriptionElement = $this->_createElement('description', $element->description, null, $elementElement);
            // elementTextContainer
            $elementTextContainerElement = $this->_createElement('elementTextContainer');
            foreach ($element->elementTexts as $elementTextId => $elementText) {
                // elementText
                $elementTextElement = $this->_createElement('elementText', null, $elementTextId);
                $textElement = $this->_createElement('text', $elementText->text, null, $elementTextElement);
                $elementTextContainerElement->appendChild($elementTextElement);
            }
            $elementElement->appendChild($elementTextContainerElement);
            $elementContainerElement->appendChild($elementElement);
        }
        $itemTypeElement->appendChild($elementContainerElement);
        $parentElement->appendChild($itemTypeElement);
    }
    
    protected function _buildFileContainerForItem(Item $item, DOMElement $parentElement)
    {
        // Return if the item has no files.
        if (!count($item->Files)) {
            return null;
        }
        
        // fileContainer
        $fileContainerElement = $this->_createElement('fileContainer');
        foreach ($item->Files as $file) {
            // file
            $fileElement = $this->_createElement('file', null, $file->id);
            $srcElement = $this->_createElement('src', WEB_ARCHIVE . "/{$file->archive_filename}", null, $fileElement);
            $authenticationElement = $this->_createElement('authentication', $file->authentication, null, $fileElement);
            $this->_buildElementSetContainerForRecord($file, $fileElement);
            $fileContainerElement->appendChild($fileElement);
        }
        $parentElement->appendChild($fileContainerElement);
    }
    
    protected function _buildCollectionForItem(Item $item, DOMElement $parentElement)
    {
        // Return if the item has no collection.
        if (!$item->Collection) {
            return null;
        }
        
        // collection
        $collectionElement = $this->_createElement('collection', null, $item->Collection->id);
        $nameElement = $this->_createElement('name', $item->Collection->name, null, $collectionElement);
        $descriptionElement = $this->_createElement('description', $item->Collection->description, null, $collectionElement);
        $parentElement->appendChild($collectionElement);
    }
    
    protected function _buildTagContainerForItem(Item $item, DOMElement $parentElement)
    {
        // Return if the item has no tags.
        if (!count($item->Tags)) {
            return null;
        }
        
        // tagContainer
        $tagContainerElement = $this->_createElement('tagContainer');
        foreach ($item->Tags as $tag) {
            // tag
            $tagElement = $this->_createElement('tag', null, $tag->id);
            $name = $this->_createElement('name', $tag->name, null, $tagElement);
            $tagContainerElement->appendChild($tagElement);
        }
        $parentElement->appendChild($tagContainerElement);
   }
}