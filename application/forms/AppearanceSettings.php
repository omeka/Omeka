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
class Omeka_Form_AppearanceSettings extends Omeka_Form
{

    private $_app;

    public function init()
    {
        parent::init();

        $this->addElement('text', 'fullsize_constraint', array(
            'label' => __('Fullsize Image Size'),
            'description' => __('Maximum fullsize image size constraint (in pixels).'), 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'thumbnail_constraint', array(
            'label' => __('Thumbnail Size'),
            'description' => __('Maximum thumbnail size constraint (in pixels).'), 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'square_thumbnail_constraint', array(
            'label' => __('Square Thumbnail Size'), 
            'description' => __('Maximum square thumbnail size constraint (in pixels).'), 
            'validators' => array('Digits'), 
            'required' => true,
        ));
        
        $this->addElement('text', 'per_page_admin', array(
            'label' => __('Items Per Page (admin)'), 
            'description' => __('Limit the number of items displayed per page in the administrative interface.'), 
            'validators' => array('Digits'), 
            'required' => true,
        ));
        
        $this->addElement('text', 'per_page_public', array(
            'label' => __('Items Per Page (public)'), 
            'description' => __('Limit the number of items displayed per page in the public interface.'), 
            'validators' => array('Digits'), 
            'required' => true,
        ));
        
        $this->addElement('checkbox', 'show_empty_elements', array(
            'label' => __('Show Empty Elements'),
            'class' => 'checkbox',
        ));
        
        $this->addDisplayGroup(
            array('fullsize_constraint', 'thumbnail_constraint', 'square_thumbnail_constraint', 'per_page_admin', 'per_page_public', 'show_empty_elements'),
            'appearance'
        );

    }

}

?>