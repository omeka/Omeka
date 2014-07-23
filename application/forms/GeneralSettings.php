<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Form
 */
class Omeka_Form_GeneralSettings extends Omeka_Form
{
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
        $this->setAttrib('id', 'settings-form');
        
        $this->addElement('text', 'site_title', array(
            'label' => __('Site Title')
        ));
        
        $this->addElement('textarea', 'description', array(
            'label' => __('Site Description'),
            'rows' => 10
        ));
        
        $this->addElement('text', 'administrator_email', array(
            'label' => __('Administrator Email'),
            'validators' => array('EmailAddress'), 
            'required' => true
        ));
        
        $this->addElement('text', 'copyright', array(
            'label' => __('Site Copyright Information')
        ));
        
        $this->addElement('text', 'author', array(
            'label' => __('Site Author Information')
        ));
        
        $this->addElement('text', 'tag_delimiter', array(
            'label' => __('Tag Delimiter'),
            'description' => __('Separate tags using this character or string. Be careful when changing this setting. You run the risk of splitting tags that contain the old delimiter.'),
        ));
        
        // Allow the tag delimiter to be a whitespace character(s) (except for 
        // new lines). The NotEmpty validator (and therefore the required flag) 
        // considers spaces to be empty. Because of this we must set the 
        // allowEmpty flag to false so Zend_Form_Element::isValid() passes an 
        // "empty" value to the validators, and then, using the Regex validator, 
        // match the value to a string containing one or more characters.
        $this->getElement('tag_delimiter')->setAllowEmpty(false);
        $this->getElement('tag_delimiter')->addValidator('regex', false, array('/^.+$/'));

        $this->addElement('text', 'path_to_convert', array(
            'label' => __('ImageMagick Directory Path')
        ));

        $this->addElement('hash', 'settings_csrf', array(
            'timeout' => 3600
        ));
        
        $this->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'tag_delimiter', 'path_to_convert'),
            'site_settings');
    }
}
