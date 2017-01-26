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

        $this->addElement('checkbox', 'use_square_thumbnail', array(
            'label' => __('Use Square Thumbnails'),
            'description' => __('Use square-cropped images by default wherever thumbnails appear in the public interface.'),
            'class' => 'checkbox'
        ));

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
            'label' => __('Results Per Page (admin)'),
            'description' => __('Limit the number of results displayed per page in the administrative interface.'),
            'validators' => array('Digits'),
            'required' => true,
        ));

        $this->addElement('text', 'per_page_public', array(
            'label' => __('Results Per Page (public)'),
            'description' => __('Limit the number of results displayed per page in the public interface.'),
            'validators' => array('Digits'),
            'required' => true,
        ));

        $this->addElement('checkbox', 'show_empty_elements', array(
            'label' => __('Show Empty Elements'),
            'class' => 'checkbox',
        ));

        $this->addElement('checkbox', 'show_element_set_headings', array(
            'label' => __('Show Element Set Headings'),
            'class' => 'checkbox',
        ));

        $this->addElement('checkbox', 'warn_unsaved_form', array(
            'label' => __('Warn Unsaved Form'),
            'description' => __('Alert curators to unsaved changes in a form if they attempt to close the browser or navigate away from the page.'),
            'class' => 'checkbox',
        ));

        $adminThemes = Theme::getAllAdminThemes();
        if (count($adminThemes) > 1 && is_allowed('Themes', 'edit')) {
            foreach ($adminThemes as &$theme) {
                $theme = $theme->title;
            }
            $this->addElement('select', Theme::ADMIN_THEME_OPTION, array(
                'label' => __('Admin Theme'),
                'multiOptions' => $adminThemes,
            ));
        }

        $this->addElement('hash', 'appearance_csrf', array(
            'timeout' => 3600
        ));

        $this->addDisplayGroup(
            array(
                'fullsize_constraint', 'thumbnail_constraint',
                'square_thumbnail_constraint',
            ),
            'derivative-constraints', array('legend' => __('Derivative Size Constraints'))
        );

        $this->addDisplayGroup(
            array(
                'use_square_thumbnail', 'per_page_admin', 'per_page_public', 
                'show_empty_elements', 'show_element_set_headings', 'warn_unsaved_form',
            ),
            'display-settings', array('legend' => __('Display Settings'))
        );

        if (count($adminThemes) > 1 && is_allowed('Themes', 'edit')) {
            $this->addDisplayGroup(
                array(
                    Theme::ADMIN_THEME_OPTION,
                ),
                'admin-themes', array('legend' => __('Admin Themes'))
            );
        }
    }

}

?>
