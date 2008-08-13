<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Helper that writes XHTML containing all the available elements and texts 
 * associated with those elements for a given item.
 *
 * @see item_show()
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View_Helper_ItemShow extends Zend_View_Helper_Abstract
{
    const ELEMENT_SET_ITEM_TYPE = 'Item Type';
    
    private $_item;
    
    private $_output;
    
    // Format of the output
    private $_outputFormat = 'Default';
    
    // Whether to show elements that do not have text
    private $_showEmptyElements = true;
    
    // The text to display if _showEmptyElements = true
    private $_emptyElementText = '[no text]';
    
    // format = default
    // show_empty_elements = true | string
    public function itemShow(Item $item, array $options = array())
    {
        $this->_item = $item;
        $this->_setOptions($options);
        $this->_buildOutput();
        return $this->_output;
    }
    
    private function _setOptions($options)
    {
        // Handle output_format option
        if (array_key_exists('output_format', $options)) {
            
            // Cast to mixed case string without whitespace
            $outputFormat = preg_replace('/\s/', 
                                         '', 
                                         ucwords((string) $options['output_format']));
            
            // Should be a valid method
            if (!method_exists($this, '_outputFormat' . $outputFormat)) {
                throw new Exception('Invalid output format.');
            }
            
            $this->_outputFormat = $outputFormat;
        }
        
        // Handle show_empty_elements option
        if (array_key_exists('show_empty_elements', $options)) {
            if (is_string($options['show_empty_elements'])) {
                $this->_emptyElementText = $options['show_empty_elements'];
            } else {
                $this->_showEmptyElements = (bool) $options['show_empty_elements'];
            }
        }
    }
    
    private function _buildOutput()
    {
        // Dynamically call the output format method
        $outputFormatMethod = '_outputFormat' . $this->_outputFormat;
        $this->$outputFormatMethod();
    }
    
    private function _getElementsBySet()
    {
        $elementsBySet = $this->_item->getAllElementsBySet();
        // Overwrite elements assigned to the "Item Type" element set with only 
        // those that belong to this item's particular item type.
        $elementsBySet[self::ELEMENT_SET_ITEM_TYPE] = $this->_item->getItemTypeElements();
        return $elementsBySet;
    }
    
    private function _getTextsByElement(Element $element)
    {
        return $this->_item->getTextsByElement($element);
    }
    
    private function _isShowable(array $texts)
    {
        if (!empty($texts) || (empty($texts) && $this->_showEmptyElements)) {
            return true;
        }
        return false;
    }
    
    // Show any text, including from an ElementText object.
    private function _show($text, Element $element = null)
    {
        $html = false;
        if ($text instanceof ElementText) {
            $text = $text->text;
            $text = apply_filters(array('Display', 'Item', $element->name, $element->set_name), $text, $this->_item, $element);
            $html = $text->html ? true : false;
        }
        return $html ? $text : htmlentities($text);
    }
    
    private function _callPrependHooks()
    {
        if (theme_is_admin()) {
            fire_plugin_hook('admin_prepend_to_item_show', $this->_item);
        } else {
            fire_plugin_hook('public_prepend_to_item_show', $this->_item);
        }
    }
    
    private function _callAppendHooks()
    {
        if (theme_is_admin()) {
            fire_plugin_hook('admin_append_to_item_show', $this->_item);
        } else {
            fire_plugin_hook('public_append_to_item_show', $this->_item);
        }
    }
    
    private function _outputFormatDefault()
    {
?>
<?php $this->_callPrependHooks(); ?>
<?php $elementsBySet = $this->_getElementsBySet(); ?>
<?php foreach ($elementsBySet as $setName => $elementsInSet): ?>
<div class="element-set">
    <h2><?php echo $setName ?></h2>
    <?php foreach ($elementsInSet as $element): ?>
    <?php $texts = $this->_getTextsByElement($element); ?>
    <?php if ($this->_isShowable($texts)): ?>
    <div class="element">
        <h3><?php echo $element['name'] ?></h3>
        <?php if (!empty($texts)): ?>
        <?php foreach ($texts as $text): ?>
        <div class="element-text"><?php echo $this->_show($text, $element); ?></div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="element-text-empty"><?php echo $this->_show($this->_emptyElementText); ?></div>
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
