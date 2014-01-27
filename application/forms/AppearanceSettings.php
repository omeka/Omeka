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

        $this->addElement('text', 'derivative_types', array(
            'label' => __('Derivative types'),
            'description' => __('This list contains the derivative types used by Omeka.')
                . ' ' . __('Default is "%sfullsize; thumbnail; square_thumbnail%s".', '<em>', '</em>')
                . ' ' . __('Other derivative types can be added (not the original).')
                . ' ' . __("Once this option saved, don't forget to set the path and the constraint values below for new derivative types."),
            'validators' => array(
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '/^[\w\;\ ]+$/',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                               __('Derivatives must contain only letters, numbers, and "_".'),
                        ),
                    ),
                ),
            ),
            'required' => true,
        ));

        $derivative_types = unserialize(get_option('derivative_types'));
        $storageDisplayGroup = array();

        $type = 'original';
        $this->addElement('text', $type . '_path', array(
            'label' => '"' . $type . '"',
            'description' => __('Subfolder where "%s" files will be saved.', $type),
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('This path cannot be empty.')
                        )
                    )
                ),
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '/^[\w\_]+$/',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                               __('Path must contain only letters, numbers, and "_".'),
                        ),
                    ),
                ),
            ),
            'required' => true,
        ));
        $storageDisplayGroup[] = $type . '_path';

        foreach ($derivative_types as $type) {
            $this->addElement('text', $type . '_path', array(
                'label' => '"' . $type . '"',
                'description' => __('Subfolder where "%s" derivative files will be saved.', $type),
                'validators' => array(
                    array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                        array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => __('This path cannot be empty.')
                            )
                        )
                    ),
                    array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                        array(
                            'pattern' => '/^[\w\_]+$/',
                            'messages' => array(
                                Zend_Validate_Regex::NOT_MATCH =>
                                   __('Path must contain only letters, numbers, and "_".'),
                            ),
                        ),
                    ),
                ),
                'required' => true,
            ));
            $storageDisplayGroup[] = $type . '_path';

            $this->addElement('text', $type . '_constraint', array(
                'description' => __('Maximum image size constraint (in pixels) or escaped ImageMagick parameters.'),
                'required' => true,
            ));
            $storageDisplayGroup[] = $type . '_constraint';

            $this->addElement('checkbox', $type . '_constraint_square', array(
                'description' => __('Check if "%s" is a square derivative.', $type),
                'class' => 'checkbox',
            ));
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
