<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 * @subpackage Omeka_View_Helper
 **/

/**
 * Helper that writes XHTML containing metadata about an item.
 * @see show_item_metadata()
 * @package OmekaThemes
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View_Helper_ItemShow extends Zend_View_Helper_Abstract
{
    /**
     * The name of the item type element set. Change this constant if the name 
     * changes in the database.
     */
    const ELEMENT_SET_ITEM_TYPE = ELEMENT_SET_ITEM_TYPE;
    
    const RETURN_HTML = 'html';
    const RETURN_ARRAY = 'array';
    
    /**
     * The Item object.
     * @var object
     */
    private $_item;
    
    /**
     * Flag to indicate whether to show elements that do not have text.
     * @see self::$_emptyElementString
     * @var bool
     */
    private $_showEmptyElements = true;
    
    /**
     * The string to display if elements without text are shown.
     * @see self::$_showEmptyElements
     * @var string
     */
    private $_emptyElementString = '[no text]';
    
    protected $_elementSetsToShow = array();
    
    protected $_returnType = self::RETURN_HTML;
    
    /**
     * Virtual constructor.
     * @param Item
     * @param array $options
     * Available options:
     * 'show_empty_elements' => bool|string Whether to show elements that 
     *     do not contain text. A string will set self::$_showEmptyElements to 
     *     true and set self::$_emptyElementString to the provided string.
     * 'show_element_sets' => array List of names of element sets to display.
     * 'return_type' => string 'array', 'html'.  Defaults to 'html'.
     * @since 1.0 Added 'show_element_sets' and 'return_type' options.
     * @return string  
     */
    public function itemShow(Item $item, array $options = array())
    {
        $this->_item = $item;
        $this->_setOptions($options);
        $output = $this->_getOutput();
        return $output;
    }
    
    /**
     * Set the options.
     * @param array $options
     * @return void
     */
    private function _setOptions(array $options)
    {
        // Handle show_empty_elements option
        if (array_key_exists('show_empty_elements', $options)) {
            if (is_string($options['show_empty_elements'])) {
                $this->_emptyElementString = $options['show_empty_elements'];
            } else {
                $this->_showEmptyElements = (bool) $options['show_empty_elements'];
            }
        }
        
        if (array_key_exists('show_element_sets', $options)) {
            $namesOfElementSetsToShow = $options['show_element_sets'];
            if (is_string($namesOfElementSetsToShow)) {
                $this->_elementSetsToShow = array_map('trim', explode(',', $namesOfElementSetsToShow));
            } else {
                $this->_elementSetsToShow = $namesOfElementSetsToShow;
            }
        }
        
        if (array_key_exists('return_type', $options)) {
            $this->_returnType = (string)$options['return_type'];
        }
    }
    
    /**
     * Get an array of all element sets containing their respective elements.
     * @uses Item::getAllElementsBySet()
     * @uses Item::getItemTypeElements()
     * @return array
     */
    private function _getElementsBySet()
    {
        $elementsBySet = $this->_item->getAllElementsBySet();
        
        // Only show the element sets that are passed in as options.
        if (!empty($this->_elementSetsToShow)) {
            $elementsBySet = array_intersect_key($elementsBySet, array_flip($this->_elementSetsToShow));
        }
        
        if ($this->_item->item_type_id) {
            // Overwrite elements assigned to the item type element set with only 
            // those that belong to this item's particular item type. This is 
            // necessary because, otherwise, all item type elements will be shown.
            $itemTypeElementSetName = item('Item Type Name') . ' ' . self::ELEMENT_SET_ITEM_TYPE;
            
            // Check to see if either the generic or specific Item Type element
            // set has been chosen, i.e. 'Item Type Metadata' or 'Document
            // Item Type Metadata', etc.
            if (!empty($this->_elementSetsToShow)) {
                if (in_array($itemTypeElementSetName, $this->_elementSetsToShow) or 
                in_array(self::ELEMENT_SET_ITEM_TYPE, $this->_elementSetsToShow)) {
                    $elementsBySet[$itemTypeElementSetName] = $this->_item->getItemTypeElements();
                }
            }
            else {
                $elementsBySet[$itemTypeElementSetName] = $this->_item->getItemTypeElements();
            }
        }
        
        // Unset the existing 'Item Type' element set b/c it shows elements for all item types.
         unset($elementsBySet[self::ELEMENT_SET_ITEM_TYPE]);
        
        return $elementsBySet;
    }
    
    /**
     * Get an array of all texts belonging to the provided element.
     * @uses Item::getTextsByElement()
     * @param Element
     * @return array
     */
    private function _getTextsByElement(Element $element)
    {
        return $this->_item->getTextsByElement($element);
    }
    
    /**
     * Determine if an element is allowed to be shown. This method also caches 
     * the current Element object and the array of the current ElementText 
     * objects. This caching occurs to avoid complexity in the output methods.
     * @todo Maybe separate caching here so it's not hiding in a seemingly 
     * unassociated method. Though, doing so would require an extra step in the 
     * output methods (i.e. <?php $this->_cache($element); ?>).
     * @param Element
     * @return bool
     */
    private function _elementIsShowable(Element $element, $texts)
    {        
        // If the condidtions are met, this element is showable.
        if (!empty($texts) 
            || (empty($texts) && $this->_showEmptyElements)) {
            return true;
        }
        // This element is not showable.
        return false;
    }
    
    /**
     * Output the default format for displaying item metadata.
     * @return void 
     */
    protected function _getOutputAsHtml()
    {
        // Prepare the metadata for display on the partial.  There should be no 
        // need for method calls by default in the view partial.
        $elementSets = $this->_getElementsBySet();
        foreach ($elementSets as $setName => $elementsInSet) {
            foreach ($elementsInSet as $key => $element) {
                $elementName = $element->name;
                $elementsInSet[$key] = array();
                $elementsInSet[$key]['element'] = $element;
                $elementsInSet[$key]['elementName'] = $element->name;
                $elementTexts = $this->_getTextsByElement($element);
                $elementsInSet[$key]['isShowable'] = $this->_elementIsShowable($element, $elementTexts);
                $elementsInSet[$key]['isEmpty'] = empty($elementTexts);
                $elementsInSet[$key]['emptyText'] = htmlentities($this->_emptyElementString);
            }
            $elementSets[$setName] = $elementsInSet;
            
            // We're done preparing the data for display, so display it.
            $varsToInject = array('elementSets'=>$elementSets, 'setName'=>$setName, 
            'elementsInSet'=>$elementsInSet, 'item'=>$this->_item);
            $this->_loadViewPartial($varsToInject);
        }
    }
    
    protected function _getOutputAsArray()
    {
        $elementSets = $this->_getElementsBySet();
        $outputArray = array();
        foreach ($elementSets as $setName => $elementsInSet) {
            $outputArray[$setName] = array();
            foreach ($elementsInSet as $key => $element) {
                $elementName = $element->name;
                $textArray = $this->view->item($this->_item, $element->set_name, $elementName, 'all');
                if (!empty($textArray[0]) or $this->_showEmptyElements) {
                    $outputArray[$setName][$elementName] = $textArray;
                }
            }
        }
        return $outputArray;
    }
    
    protected function _getOutput()
    {
        switch ($this->_returnType) {
            case self::RETURN_HTML:
                ob_start();
                $this->_getOutputAsHtml();
                $output = ob_get_contents();
                ob_end_clean();
                return $output;
                break;
            case self::RETURN_ARRAY:
                return $this->_getOutputAsArray();
                break;
            default:
                throw new Exception('Invalid return type!');
                break;
        }
    }
    
    /**
     * @todo Could pass the name of the partial in as an argument rather than hard
     * coding it.  That would allow us to use arbitrary partials for purposes such as 
     * repackaging the data for RSS/XML or other data formats.
     * 
     * @param array
     * @return void
     **/
    private function _loadViewPartial($vars = array())
    {
        return common('item-metadata', $vars, 'items');
    }
}
