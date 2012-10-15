<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Security settings form.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_SecuritySettings extends Omeka_Form
{
    
    public function init()
    {
        parent::init();
        $this->setAttrib('id', 'settings-form');
        
        $this->addElement('checkbox', File::DISABLE_DEFAULT_VALIDATION_OPTION,
            array(
                'label' => __('Disable File Upload Validation'),
                'checked' => get_option(File::DISABLE_DEFAULT_VALIDATION_OPTION),
                'description' => __('Check this field if you would like to allow any file to be uploaded to Omeka.')
            )
        );
        
        $this->addElement('textarea', Omeka_Validate_File_Extension::WHITELIST_OPTION,
            array(
                'label' => __('Allowed File Extensions'),
                'description' => __('List of allowed extensions for file uploads.'),
                'value' => get_option(Omeka_Validate_File_Extension::WHITELIST_OPTION),
                'cols'=>50, 
                'rows'=>5
            )
        );
        
        $this->addElement('textarea', Omeka_Validate_File_MimeType::WHITELIST_OPTION,
            array(
                'label' => __('Allowed File Types'),
                'description' => __('List of allowed MIME types for file uploads'),
                'value' => get_option(Omeka_Validate_File_MimeType::WHITELIST_OPTION),
                'cols' => 50, 
                'rows' => 13
            )
        );
        
        $this->addElement('text', Omeka_Captcha::PUBLIC_KEY_OPTION,
            array(
                'label' => __('ReCaptcha Public Key'),
                'description' => __('Public key from recaptcha.net. Both this and the private key must be filled in to secure public forms.'),
                'value' => get_option(Omeka_Captcha::PUBLIC_KEY_OPTION)
            )
        );

        $this->addElement('text', Omeka_Captcha::PRIVATE_KEY_OPTION,
            array(
                'label' => __('ReCaptcha Private Key'),
                'description' => __('Private key from recaptcha.net. Both this and the public key must be filled in to secure public forms.'),
                'value' => get_option(Omeka_Captcha::PRIVATE_KEY_OPTION)
            )
        );
        
        $this->addElement('checkbox', 'html_purifier_is_enabled', array(
            'checked' => (boolean)get_option('html_purifier_is_enabled'),
            'description' => 'Check this field if you would like to filter HTML elements or attributes from form input.',
            'label' => 'Enable HTML Filtering'
        ));
        
        $this->addElement('textarea', 'html_purifier_allowed_html_elements', array(
            'value' => get_option('html_purifier_allowed_html_elements'),
            'label' => 'Allowed HTML Elements',
            'description' => 'List of allowed HTML elements in form input.',
            'cols' => 50, 
            'rows' =>  5
        ));
        
        $this->addElement('textarea', 'html_purifier_allowed_html_attributes', array(
            'value' => get_option('html_purifier_allowed_html_attributes'),
            'label' => 'Allowed HTML Attributes',
            'description' => 'List of allowed HTML attributes in form input.',
            'cols' => 50, 
            'rows' => 5
        ));

        $this->addDisplayGroup(
            array(File::DISABLE_DEFAULT_VALIDATION_OPTION, Omeka_Validate_File_Extension::WHITELIST_OPTION,
                Omeka_Validate_File_MimeType::WHITELIST_OPTION, Omeka_Captcha::PUBLIC_KEY_OPTION, Omeka_Captcha::PRIVATE_KEY_OPTION,
                'html_purifier_is_enabled', 'html_purifier_allowed_html_elements', 'html_purifier_allowed_html_attributes'),
            'security_settings');

    }
}
