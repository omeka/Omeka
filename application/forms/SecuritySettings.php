<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Security settings form.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
                'rows' => 5
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
        
        $this->addElement('submit', 'security_submit', array(
            'label' => __('Save Changes')
        ));
    }
}
