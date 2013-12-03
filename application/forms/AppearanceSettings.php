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

        $this->addElement('text', 'storage_paths', array(
            'label' => __('Derivative types and paths'),
            'description' => __('This list contains the derivative types used by Omeka.')
                . ' ' . __('Default is "%soriginal = original; fullsize = fullsize; thumbnail = thumbnails; square_thumbnail = square_thumbnails%s".', '<em>', '</em>')
                . ' ' . __('Other derivative types can be added with the same format "type = folder".')
                . ' ' . __("Once this option saved, don't forget to set the constraint value below for new derivative types."),
            'validators' => array(
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '/^[\w\=\;\ ]+$/',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                               __('Derivatives must contain only letters, numbers, and "_".'),
                        ),
                    ),
                ),
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '/original/',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                               __('Derivatives must contain the type "original". Other default types are recommanded.'),
                        ),
                    ),
                ),
            ),
            'required' => true,
        ));

        $storage_paths = unserialize(get_option('storage_paths'));
        unset($storage_paths['original']);
        $storageDisplayGroup = array();
        foreach ($storage_paths as $type => $path) {
            $this->addElement('text', $type . '_constraint', array(
                'label' => __('"%s" Image Size', $type),
                'description' => __('Maximum image size constraint (in pixels) or escaped ImageMagick parameters.'),
                'required' => true,
            ));
            $this->addElement('checkbox', $type . '_constraint_square', array(
                'description' => __('Check if "%s" is a square derivative.', $type),
                'class' => 'checkbox',
            ));
            $storageDisplayGroup[] = $type . '_constraint';
            $storageDisplayGroup[] = $type . '_constraint_square';
        }

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

        $this->addDisplayGroup(
            $storageDisplayGroup,
            'derivative-constraints',
            array('legend' => __('Derivative Images Formats'))
        );

        $this->addDisplayGroup(
            array('per_page_admin', 'per_page_public', 'show_empty_elements'),
            'display-settings',
            array('legend' => __('Display Settings'))
        );
    }
}

?>
