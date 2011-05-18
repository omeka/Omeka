<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Forms
 * @access private
 **/

/**
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Forms
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 **/
class Omeka_Form_GeneralSettings extends Omeka_Form
{
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
        $this->setAttrib('id', 'settings-form');
        
        $this->addElement('text', 'site_title', array(
            'label' => 'Site Title'
        ));
        
        $this->addElement('textarea', 'description', array(
            'label' => 'Site Description',
        ));
        
        $this->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email',
            'validators' => array('EmailAddress'), 
            'required' => true
        ));
        
        $this->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information'
        ));
        
        $this->addElement('text', 'author', array(
            'label' => 'Site Author Information'
        ));
        
        $this->addElement('text', 'tag_delimiter', array(
            'label' => 'Tag Delimiter', 
            'description' => 'Separate tags using this character(s). Be careful when changing this setting. You run the risk of splitting tags that contain the old delimiter.', 
        ));
        
        // Allow the tag delimiter to be a whitespace character(s) (except for 
        // new lines). The NotEmpty validator (and therefore the required flag) 
        // considers spaces to be empty. Because of this we must set the 
        // allowEmpty flag to false so Zend_Form_Element::isValid() passes an 
        // "empty" value to the validators, and then, using the Regex validator, 
        // match the value to a string containing one or more characters.
        $this->getElement('tag_delimiter')->setAllowEmpty(false);
        $this->getElement('tag_delimiter')->addValidator('regex', false, array('/^.+$/'));
        
        $this->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size',
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size',
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size', 
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('checkbox', 'show_empty_elements', array(
            'label' => 'Show Empty Elements',
            'class' => 'checkbox'
        ));

        $this->addElement('checkbox', 'enable_prototype', array(
            'label' => 'Enable Prototype',
            'class' => 'checkbox',
            'description' => 'Enable the Prototype JavaScript library. This may be required for some older plugins.'
        ));

        $this->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path'
        ));
        
        $this->addElement('submit', 'settings_submit', array(
            'label' => 'Save Settings'
        ));
        
        $this->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'tag_delimiter', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements',
                  'enable_prototype', 'path_to_convert'),
            'site_settings');
        
        $this->addDisplayGroup(
            array('settings_submit'), 
            'submit');
    }
}
