<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @access private
 */

/**
 * Abstract class that encapsulates default behavior for retrieving lists of 
 * metadata for any record that uses ActsAsElementText.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Omeka_View_Helper
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
abstract class Omeka_View_Helper_RecordMetadataList extends Zend_View_Helper_Abstract
{   
    const RETURN_HTML = 'html';
    const RETURN_ARRAY = 'array';
    
    /**
     * The Item object.
     * @var object
     */
    protected $_record;
    
    /**
     * Flag to indicate whether to show elements that do not have text.
     * @see self::$_emptyElementString
     * @var boolean
     */
    protected $_showEmptyElements = true;
    
    /**
     * String to display if elements without text are shown.
     * @see self::$_showEmptyElements
     * @var string
     */
    protected $_emptyElementString;
    
    /**
     * Element sets to list.
     *
     * @var array
     */
    protected $_elementSetsToShow = array();
    
    /**
     * Type of data to return.
     *
     * @var string
     */
    protected $_returnType = self::RETURN_HTML;
    
    /**
     * Get the record metadata list.
     * 
     * @param Omeka_Record $record Record to retrieve metadata from.
     * @param array $options
     *  Available options:
     *  - show_empty_elements' => bool|string Whether to show elements that 
     *    do not contain text. A string will set self::$_showEmptyElements to 
     *    true and set self::$_emptyElementString to the provided string.
     *  - 'show_element_sets' => array List of names of element sets to display.
     *  - 'return_type' => string 'array', 'html'.  Defaults to 'html'.
     * @since 1.0 Added 'show_element_sets' and 'return_type' options.
     * @return string|array 
     */
    protected function _getList(Omeka_Record $record, array $options = array())
    {
        $this->_record = $record;
        $this->_setOptions($options);
        $output = $this->_getOutput();
        return $output;
    }
    
    /**
     * Set the options.
     *
     * @param array $options
     * @return void
     */
    protected function _setOptions(array $options)
    {
        // Set a default for show_empty_elements based on site setting
        $this->_showEmptyElements = (bool) get_option('show_empty_elements');
        $this->_emptyElementString = __('[no text]');
        
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
     * 
     * @uses Item::getAllElementsBySet()
     * @uses Item::getItemTypeElements()
     * @return array
     */
    protected function _getElementsBySet()
    {
        $elementsBySet = $this->_record->getAllElementsBySet();
        
        // Only show the element sets that are passed in as options.
        if (!empty($this->_elementSetsToShow)) {
            $elementsBySet = array_intersect_key($elementsBySet, array_flip($this->_elementSetsToShow));
        }
        
        return $elementsBySet;
    }
    
    /**
     * Get an array of all texts belonging to the provided element.
     * @uses Item::getTextsByElement()
     * @param Element $element
     * @return array
     */
    protected function _getTextsByElement(Element $element)
    {
        return $this->_record->getTextsByElement($element);
    }
    
    /**
     * Determine if an element is allowed to be shown. This method also caches 
     * the current Element object and the array of the current ElementText 
     * objects. This caching occurs to avoid complexity in the output methods.
     * @todo Maybe separate caching here so it's not hiding in a seemingly 
     * unassociated method. Though, doing so would require an extra step in the 
     * output methods (i.e. <?php $this->_cache($element); ?>).
     * @param Element $element
     * @param array $texts
     * @return boolean
     */
    protected function _elementIsShowable(Element $element, $texts)
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
     * Output the default format for displaying record metadata.
     * @return void 
     */
    protected function _getOutputAsHtml()
    {
        // Prepare the metadata for display on the partial.  There should be no 
        // need for method calls by default in the view partial.
        $elementSets = $this->_getElementsBySet();
        foreach ($elementSets as $setName => $elementsInSet) {
            $setIsEmpty = true;
            foreach ($elementsInSet as $key => $element) {
                $elementName = $element->name;
                $elementsInSet[$key] = array();
                $elementsInSet[$key]['element'] = $element;
                $elementsInSet[$key]['elementName'] = $element->name;
                $elementTexts = $this->_getFormattedElementText($this->_record, $element->set_name, $element->name);
                $elementsInSet[$key]['isShowable'] = $this->_elementIsShowable($element, $elementTexts);
                $elementsInSet[$key]['isEmpty'] = empty($elementTexts);
                $elementsInSet[$key]['emptyText'] = html_escape($this->_emptyElementString);
                $elementsInSet[$key]['texts'] = $elementTexts;
                if ($setIsEmpty && !$elementsInSet[$key]['isEmpty']) {
                    $setIsEmpty = false;
                } 
            }
            $elementSets[$setName] = $elementsInSet;
            
            // We're done preparing the data for display, so display it.
            if (!$setIsEmpty || $this->_showEmptyElements){
            $varsToInject = array('elementSets'=>$elementSets, 'setName'=>$setName, 
            'elementsInSet'=>$elementsInSet, 'record'=>$this->_record);
            $this->_loadViewPartial($varsToInject);
            }
        }
    }
    
    /**
     * Return a formatted version of the requested element.
     *
     * @param Omeka_Record $record
     * @param string $elementSetName
     * @param string $elementName
     * @return string
     */
    abstract protected function _getFormattedElementText($record, $elementSetName, $elementName);
    
    /**
     * Get the metadata list as a PHP array.
     *
     * @return array
     */
    protected function _getOutputAsArray()
    {
        $elementSets = $this->_getElementsBySet();
        $outputArray = array();
        foreach ($elementSets as $setName => $elementsInSet) {
            $outputArray[$setName] = array();
            foreach ($elementsInSet as $key => $element) {
                $elementName = $element->name;
                $textArray = $this->_getFormattedElementText($this->_record, $element->set_name, $elementName);
                if (!empty($textArray[0]) or $this->_showEmptyElements) {
                    $outputArray[$setName][$elementName] = $textArray;
                }
            }
        }
        return $outputArray;
    }
    
    /**
     * Get the metadata list.
     *
     * @return string|array
     */
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
     * Load a view partial to display the data.
     *
     * @todo Could pass the name of the partial in as an argument rather than 
     * hardcoding it.  That would allow us to use arbitrary partials for 
     * purposes such as repackaging the data for RSS/XML or other data formats.
     * 
     * @param array $vars
     * @return void
     */
    abstract protected function _loadViewPartial($vars = array());
}
