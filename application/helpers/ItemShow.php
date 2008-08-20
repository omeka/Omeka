<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Helper that writes XHTML containing metadata about an item.
 * @see show_item_metadata()
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View_Helper_ItemShow extends Zend_View_Helper_Abstract
{
    /**
     * The name of the item type element set. Change this constant if the name 
     * changes in the database.
     */
    const ELEMENT_SET_ITEM_TYPE = 'Item Type';
    
    /**
     * The prefix of the delegated methods that output metadata. If this changes 
     * all output methods must be changed as well.
     */
    const OUTPUT_METHOD_PREFIX = '_output';
    
    /**
     * The Item object.
     * @var object
     */
    private $_item;
    
    /**
     * The cache of the current Element object.
     * @see self::_elementIsShowable()
     * @var object
     */
    private $_currentElement;
    
    /**
     * The cache of the current array of ElementText objects.
     * @see self::_elementIsShowable()
     * @var array
     */
    private $_currentElementTexts;
    
    /**
     * The format of the output.
     * @var string
     */
    private $_outputFormat = 'Default';
    
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
    
    /**
     * Virtual constructor.
     * @param Item
     * @param array $options
     * Available options:
     * 'output_format' => string Must match the ending of a 
     * 'show_empty_elements' => bool|string Whether to show elements that 
     *     do not contain text. A string will set self::$_showEmptyElements to 
     *     true and set self::$_emptyElementString to the provided string.
     * @return void View helpers normally return a string here, but this helper 
     * outputs directly through the delegated self::_output*() methods.
     */
    public function itemShow(Item $item, array $options = array())
    {
        $this->_item = $item;
        $this->_setOptions($options);
        $this->_delegateToOutput();
    }
    
    /**
     * Set the options.
     * @param array $options
     * @return void
     */
    private function _setOptions(array $options)
    {
        // Handle output_format option.
        if (array_key_exists('output_format', $options)) {
            
            // Cast to a mixed case string without whitespace. For example, 
            // "definition list" changes to "DefinitionList".
            $outputFormat = preg_replace('/\s/', 
                                         '', 
                                         ucwords((string) $options['output_format']));
            
            // Throw an exception if this is not a valid output method.
            if (!method_exists($this, self::OUTPUT_METHOD_PREFIX . $outputFormat)) {
                throw new Exception('Invalid output format.');
            }
            
            $this->_outputFormat = $outputFormat;
        }
        
        // Handle show_empty_elements option
        if (array_key_exists('show_empty_elements', $options)) {
            if (is_string($options['show_empty_elements'])) {
                $this->_emptyElementString = $options['show_empty_elements'];
            } else {
                $this->_showEmptyElements = (bool) $options['show_empty_elements'];
            }
        }
    }
    
    /**
     * Delegate to the methods that output metadata.
     * @return void
     */
    private function _delegateToOutput()
    {
        $outputMethod = self::OUTPUT_METHOD_PREFIX . $this->_outputFormat;
        $this->$outputMethod();
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
        
         // Unset the existing 'Item Type' element set b/c it shows elements for all item types.
         unset($elementsBySet[self::ELEMENT_SET_ITEM_TYPE]);
        
        if ($this->_item->item_type_id) {
            // Overwrite elements assigned to the item type element set with only 
            // those that belong to this item's particular item type. This is 
            // necessary because, otherwise, all item type elements will be shown.
            $itemTypeElementSetName = item('Item Type Name') . ' ' . self::ELEMENT_SET_ITEM_TYPE;
            $elementsBySet[$itemTypeElementSetName] = $this->_item->getItemTypeElements();
        }
        
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
    private function _elementIsShowable(Element $element)
    {
        // Cache the current Element object.
        $this->_currentElement = $element;
        // Cache the current array of ElementText objects.
        $this->_currentElementTexts = $this->_getTextsByElement($element);
        // If the condidtions are met, this element is showable.
        if (!empty($this->_currentElementTexts) 
            || (empty($this->_currentElementTexts) && $this->_showEmptyElements)) {
            return true;
        }
        // This element is not showable.
        return false;
    }
    
    /**
     * Prepare text for display by applying filters to the text and returning an 
     * escaped or raw (HTML) string.
     * @param string|ElementText
     * @return string
     */
    private function _prepareText($text)
    {
        // The HTML flag is false by default.
        $html = false;
        // Set variables id $text is an instance of ElementText
        if ($text instanceof ElementText) {
            $text = $text->text;
            $html = $text->html ? true : false;
        }
        // Apply filters. Pass the text string, the Item object, and the current 
        // Element object.
        $text = apply_filters(array('Display', 
                                    'Item', 
                                    $this->_currentElement->name, 
                                    $this->_currentElement->set_name), 
                              $text, 
                              $this->_item, 
                              $this->_currentElement);
        // Return an escaped string or a raw (HTML) string.
        return $html ? $text : htmlentities($text);
    }
    
    /**
     * Fire prepend hooks.
     * @todo Abstract this out into a global helper function.
     * @see self::_callAppendHooks()
     * @return void
     */
    private function _callPrependHooks()
    {
        if (theme_is_admin()) {
            fire_plugin_hook('admin_prepend_to_item_show', $this->_item);
        } else {
            fire_plugin_hook('public_prepend_to_item_show', $this->_item);
        }
    }
    
    /**
     * Fire append hooks.
     * @todo Abstract this out into a global helper function.
     * @see self::_callPrependHooks()
     * @return void
     */
    private function _callAppendHooks()
    {
        if (theme_is_admin()) {
            fire_plugin_hook('admin_append_to_item_show', $this->_item);
        } else {
            fire_plugin_hook('public_append_to_item_show', $this->_item);
        }
    }
    
    /**
     * Convert the element set name and element name to a valid CSS ID. Must use 
     * both names because their combination is unique.
     * @uses text_to_id()
     * @return string
     */
    private function _getCurrentElementCssId()
    {
        $elementName = $this->_currentElement->name;
        $elementSetName = $this->_currentElement->set_name;
        return text_to_id("$elementSetName $elementName");
    }
    
    /**
     * Output the default format for displaying item metadata.
     * @return void 
     */
    // You must call self::_elementIsShowable() immediately after the 
    // $elementsInSet loop, at least if you want to take advantage of the 
    // Element and ElementText caching and use self::_prepareText().
    private function _outputDefault()
    {
?>
<?php $this->_callPrependHooks(); ?>
<?php foreach ($this->_getElementsBySet() as $setName => $elementsInSet): ?>
<div class="element-set">
    <h2><?php echo $setName ?></h2>
    <?php foreach ($elementsInSet as $element): ?>
    <?php if ($this->_elementIsShowable($element)): ?>
    <div id="<?php echo $this->_getCurrentElementCssId(); ?>" class="element">
        <h3><?php echo $this->_currentElement->name; ?></h3>
        <?php if (!empty($this->_currentElementTexts)): ?>
        <?php foreach ($this->_currentElementTexts as $text): ?>
        <div class="element-text"><?php echo $this->_prepareText($text); ?></div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="element-text-empty"><?php echo $this->_prepareText($this->_emptyElementString); ?></div>
        <?php endif; ?>
    </div><!-- end element -->
    <?php endif; ?>
    <?php endforeach; ?>
</div><!-- end element-set -->
<?php endforeach; ?>
<?php $this->_callAppendHooks(); ?>
<?php
    }
}
