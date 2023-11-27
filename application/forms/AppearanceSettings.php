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
            'description' => __('Include empty elements in show pages.'),
            'class' => 'checkbox',
        ));

        $this->addElement('checkbox', 'show_element_set_headings', array(
            'label' => __('Show Element Set Headings'),
            'description' => __('Include element set headings in show pages and in drop-down lists.'),
            'class' => 'checkbox',
        ));

        $this->addElement('checkbox', 'link_to_file_metadata', array(
            'label' => __('Link to File Metadata'),
            'description' => __('Have item files link to the file metadata instead of the file directly.'),
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
                'use_square_thumbnail', 'link_to_file_metadata', 'per_page_admin', 'per_page_public',
                'show_empty_elements', 'show_element_set_headings',
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
