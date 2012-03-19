<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Abstract base class for creating omeka-xml output formats.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
abstract class Omeka_Output_Xml_Abstract extends Omeka_Output_Xml
{
    /**
     * XML Schema instance namespace URI.
     */
    const XMLNS_XSI            = 'http://www.w3.org/2001/XMLSchema-instance';
    
    /**
     * Omeka-XML namespace URI.
     */
    const XMLNS                = 'http://omeka.org/schemas/omeka-xml/v4';
    
    /**
     * Omeka-XML XML Schema URI.
     */
    const XMLNS_SCHEMALOCATION = 'http://omeka.org/schemas/omeka-xml/v4/omeka-xml-4-1.xsd';
    
    /**
     * This class' contextual record(s).
     * @var array|Omeka_Record
     */
    protected $_record;

    /**
     * The context of this DOMDocument. Determines how buildNode() builds the 
     * elements. Valid contexts include: item, file.
     * 
     * @var string
     */
    protected $_context;
    
    /**
     * The final document object.
     * @var DOMDocument
     */
    protected $_doc;
    
    /**
     * The node built and set in child::_buildNode()
     * @var DOMNode
     */
    protected $_node;
    
    /**
     * Abstract method. child::_buildNode() should set self::$_node.
     */
    abstract protected function _buildNode();
    
    /**
     * @param Omeka_Record|array $record
     * @param string $context The context of this DOM document.
     */
    public function __construct($record, $context)
    {
        $this->_record = $record;
        $this->_context = $context;
        $this->_doc = new DOMDocument('1.0', 'UTF-8');
        $this->_doc->formatOutput = true;
        $this->_buildNode();
    }
    
    /**
     * Get the document object.
     * 
     * @return DOMDocument
     */
    public function getDoc()
    {
        $this->_doc->appendChild($this->_setRootElement($this->_node));
        return $this->_doc;
    }
    
    /**
     * Set an element as root.
     * 
     * @param DOMElement $rootElement
     * @return DOMElement The root element, including required attributes.
     */
    protected function _setRootElement($rootElement)
    {
        $rootElement->setAttribute('xmlns', self::XMLNS);
        $rootElement->setAttribute('xmlns:xsi', self::XMLNS_XSI);
        $rootElement->setAttribute('xsi:schemaLocation', self::XMLNS . ' ' . self::XMLNS_SCHEMALOCATION);
        $rootElement->setAttribute('uri', $this->_buildUrl());
        $rootElement->setAttribute('accessDate', date('c'));
        return $rootElement;
    }
    
    /**
     * Create a DOM element.
     * 
     * @param string $name The name of the element.
     * @param null|string The value of the element.
     * @param null|int The id attribute of the element.
     * @param null|DOMElement The parent element.
     * @return DOMElement
     */
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
     * Set the pagination node for container elements
     *
     * @param DOMElement The parent container element.
     * @return void
     */
    protected function _setContainerPagination(DOMElement $parentElement)
    {
        // Return if the pagination data is not registered.
        if (!Zend_Registry::isRegistered('pagination')) {
            return;
        }
        $pagination = Zend_Registry::get('pagination');
        $miscellaneousContainerElement = $this->_createElement('miscellaneousContainer', null, null, $parentElement);
        $paginationElement = $this->_createElement('pagination', null, null, $miscellaneousContainerElement);
        $this->_createElement('pageNumber',   $pagination['page'],          null, $paginationElement);
        $this->_createElement('perPage',      $pagination['per_page'],      null, $paginationElement);
        $this->_createElement('totalResults', $pagination['total_results'], null, $paginationElement);
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
        $elementSets = array();
        $itemType = array();
        
        // Get all element texts associated with the provided record.
        $elementTexts = $record->getElementTextRecords();
        foreach ($elementTexts as $elementText) {
            
            // Get associated element and element set records.
            $element = get_db()->getTable('Element')->find($elementText->element_id);
            $elementSet = get_db()->getTable('ElementSet')->find($element->element_set_id);
            
            // Differenciate between the element sets and the "Item Type 
            // Metadata" pseudo element set.
            if (ELEMENT_SET_ITEM_TYPE == $elementSet->name) {
                $itemType['elements'][$element->id]['name'] = $element->name;
                $itemType['elements'][$element->id]['description'] = $element->description;
                $itemType['elements'][$element->id]['elementTexts'][$elementText->id]['text'] = $elementText->text;
            } else {
                $elementSets[$elementSet->id]['name'] = $elementSet->name;
                $elementSets[$elementSet->id]['description'] = $elementSet->description;
                $elementSets[$elementSet->id]['elements'][$element->id]['name'] = $element->name;
                $elementSets[$elementSet->id]['elements'][$element->id]['description'] = $element->description;
                $elementSets[$elementSet->id]['elements'][$element->id]['elementTexts'][$elementText->id]['text'] = $elementText->text;
            }
        }
        
        // Return the item type metadata.
        if ($getItemType) {
            $itemType['id'] = $record->Type->id;
            $itemType['name'] = $record->Type->name;
            $itemType['description'] = $record->Type->description;
            return $itemType;
        }
        
        // Return the element sets metadata.
        return $elementSets;
    }
    
    /**
     * Build an elementSetContainer element in a record (item or file) context.
     * 
     * @param Omeka_Record $record The record from which to build element sets.
     * @param DOMElement $parentElement The element set container will append to 
     * this element.
     * @return void|null
     */
    protected function _buildElementSetContainerForRecord(Omeka_Record $record, DOMElement $parentElement)
    {
        $elementSets = $this->_getElemetSetsByElementTexts($record);
        
        // Return if there are no element sets.
        if (!$elementSets) {
            return null;
        }
        
        // elementSetContainer
        $elementSetContainerElement = $this->_createElement('elementSetContainer');
        foreach ($elementSets as $elementSetId => $elementSet) {
             // elementSet
            $elementSetElement = $this->_createElement('elementSet', null, $elementSetId);
            $nameElement = $this->_createElement('name', $elementSet['name'], null, $elementSetElement);
            $descriptionElement = $this->_createElement('description', $elementSet['description'], null, $elementSetElement);
            // elementContainer
            $elementContainerElement = $this->_createElement('elementContainer');
            foreach ($elementSet['elements'] as $elementId => $element) {
                // Exif data may contain invalid XML characters. Avoid encoding 
                // errors by skipping relevent elements.
                if ('Omeka Image File' == $elementSet['name'] && ('Exif Array' == $element['name'] || 'Exif String' == $element['name'])) {
                    continue;
                }
                // element
                $elementElement = $this->_createElement('element', null, $elementId);
                $nameElement = $this->_createElement('name', $element['name'], null, $elementElement);
                $descriptionElement = $this->_createElement('description', $element['description'], null, $elementElement);
                // elementTextContainer
                $elementTextContainerElement = $this->_createElement('elementTextContainer');
                foreach ($element['elementTexts'] as $elementTextId => $elementText) {
                    // elementText
                    $elementTextElement = $this->_createElement('elementText', null, $elementTextId);
                    $textElement = $this->_createElement('text', $elementText['text'], null, $elementTextElement);
                    $elementTextContainerElement->appendChild($elementTextElement);
                }
                $elementElement->appendChild($elementTextContainerElement);
                $elementContainerElement->appendChild($elementElement);
            }
            $elementSetElement->appendChild($elementContainerElement);
            $elementSetContainerElement->appendChild($elementSetElement);
        }
        $parentElement->appendChild($elementSetContainerElement);
    }
    
    /**
     * Build an itemType element in an item context.
     * 
     * @param Item $item The item from which to build the item type.
     * @param DOMElement $parentElement The item type will append to this element.
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
        $itemTypeElement = $this->_createElement('itemType', null, $itemType['id']);
        $nameElement = $this->_createElement('name', $itemType['name'], null, $itemTypeElement);
        $descriptionElement = $this->_createElement('description', $itemType['description'], null, $itemTypeElement);
        
        // Do not append elements if no element texts exist for this item type.
        if (isset($itemType['elements'])) {
            // elementContainer
            $elementContainerElement = $this->_createElement('elementContainer');
            foreach ($itemType['elements'] as $elementId => $element) {
                // element
                $elementElement = $this->_createElement('element', null, $elementId);
                $nameElement = $this->_createElement('name', $element['name'], null, $elementElement);
                $descriptionElement = $this->_createElement('description', $element['description'], null, $elementElement);
                // elementTextContainer
                $elementTextContainerElement = $this->_createElement('elementTextContainer');
                foreach ($element['elementTexts'] as $elementTextId => $elementText) {
                    // elementText
                    $elementTextElement = $this->_createElement('elementText', null, $elementTextId);
                    $textElement = $this->_createElement('text', $elementText['text'], null, $elementTextElement);
                    $elementTextContainerElement->appendChild($elementTextElement);
                }
                $elementElement->appendChild($elementTextContainerElement);
                $elementContainerElement->appendChild($elementElement);
            }
            $itemTypeElement->appendChild($elementContainerElement);
        }
        $parentElement->appendChild($itemTypeElement);
    }
    
    /**
     * Build a fileContainer element in an item context.
     * 
     * @param Item $item The item from which to build the file container.
     * @param DOMElement $parentElement The file container will append to this 
     * element.
     * @return void|null
     */
    protected function _buildFileContainerForItem(Item $item, DOMElement $parentElement)
    {
        // Return if the item has no files.
        if (!count($item->Files)) {
            return null;
        }
        
        // fileContainer
        $fileContainerElement = $this->_createElement('fileContainer');
        foreach ($item->Files as $file) {
            $fileOmekaXml = new Omeka_Output_Xml_File($file, $this->_context);
            $fileElement = $this->_doc->importNode($fileOmekaXml->_node, true);
            $fileContainerElement->appendChild($fileElement);
        }
        $parentElement->appendChild($fileContainerElement);
    }
    
    /**
     * Build a collection element in an item context.
     * 
     * @param Item $item The item from which to build the collection.
     * @param DOMElement $parentElement The collection will append to this 
     * element.
     * @return void|null
     */
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
        $collectorContainerElement = $this->_createElement('collectorContainer');
        foreach ($item->Collection->getCollectors() as $collector) {
            $collectorElement = $this->_createElement('collector', $collector, null, $collectorContainerElement);
        }
        $collectionElement->appendChild($collectorContainerElement);
        $parentElement->appendChild($collectionElement);
    }
    
    /**
     * Build a tagContainer element in an item context.
     * 
     * @param Item $item The item from which to build the tag container.
     * @param DOMElement $parentElement The tag container will append to this 
     * element.
     * @return void|null
     */
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
   
    /**
    * Build an itemContainer element in a collection context.
    * 
    * @param Collection $collection The collection from which to build the item 
    * container.
    * @param DOMElement $parentElement The item container will append to this 
    * element.
    * @return void|null
    */
    protected function _buildItemContainerForCollection(Collection $collection, DOMElement $parentElement)
    {
        $nameElement = $this->_createElement('name', $collection->name, null, $parentElement);
        $descriptionElement = $this->_createElement('description', $collection->description, null, $parentElement);
        $collectorContainerElement = $this->_createElement('collectorContainer');
        foreach ($collection->getCollectors() as $collector) {
            $collectorElement = $this->_createElement('collector', $collector, null, $collectorContainerElement);
        }
        $parentElement->appendChild($collectorContainerElement );
        
        // Get items belonging to this collection.
        $items = get_db()->getTable('Item')->findBy(array('collection' => $collection->id));
        
        // Return if the collection has no items.
        if (!$items) {
            return null;
        }
        
        // itemContainer
        $collectionOmekaXml = new Omeka_Output_Xml_ItemContainer($items, 'collection');
        $itemContainerElement = $this->_doc->importNode($collectionOmekaXml->_node, true);
        $parentElement->appendChild($itemContainerElement);
    }
   
   /**
    * Create a Tag URI to uniquely identify this Omeka XML instance.
    *
    * @return string
    */
   protected function _buildTagUri()
   {
       $uri = Zend_Uri::factory(abs_uri());
       $tagUri = 'tag:' . $uri->getHost() . ',' . date('Y-m-d') . ':' . $uri->getPath();
       return $tagUri;
   }
   
   /**
    * Create a absolute URI containing the current query string.
    *
    * @return string
    */
   protected function _buildUrl()
   {
       $uri = Zend_Uri::factory(abs_uri());
       $uri->setQuery($_GET);
       return $uri->getUri();
   }
}
