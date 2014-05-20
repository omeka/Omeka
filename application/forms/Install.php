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
class Omeka_Form_Install extends Omeka_Form
{
    const DEFAULT_TAG_DELIMITER = ',';
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    const DEFAULT_SHOW_EMPTY_ELEMENTS = true;
    const DEFAULT_USER_NAME = 'Super User';
        
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
            
        $this->addElement('text', 'username', array(
            'label' => __('Username'),
            'description' => __('must be 30 characters or fewer with no whitespace'), 
            'validators' => array(
                array('StringLength', false, array(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH)), 
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '#^[a-zA-Z0-9.*@+!\-_%\#\^&$]*$#u',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                                __('Whitespace is not allowed. Only these special characters may be used: %s', ' + ! @ # $ % ^ & * . - _' )
                        )
                    )
                )
            ), 
            'required' => true
        ));
        
        $this->addElement('password', 'password', array(
            'label' => __('Password'),
            'description' => __('must be at least 6 characters'), 
            'validators' => array(
                array('validator' => 'NotEmpty', 'options' => array(
                    'messages' => array(
                        'isEmpty' => 'Password is required.'
                    )
                )),
                array('validator' => 'Confirmation', 'options' => array(
                    'field' => 'password_confirm',
                    'messages' => array(
                        'notMatch' => "Typed passwords do not match.")
                )),
                array('validator' => 'StringLength', 'options' => array(
                    'min' => User::PASSWORD_MIN_LENGTH,
                    'messages' => array(
                        'stringLengthTooShort' => "Password must be at least %min% characters in length.")
                ))
            ),
            'required' => true
        ));
        
        $this->addElement('password', 'password_confirm', array(
            'label' => __('Re-type the Password'),
            'description' => __('Confirm your password.'),
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'options' => array(
                    'messages' => array(
                        'isEmpty' => 'Password confirmation is required.'
                    )
                ))
            )
        ));
        
        $this->addElement('text', 'super_email', array(
            'label' => __('Email'),
            'validators' => array('EmailAddress'), 
            'required' => true, 
        ));
        
        $this->addElement('text', 'site_title', array(
            'label' => __('Site Title'),
            'required' => true
        ));
        
        $this->addElement('textarea', 'description', array(
            'label' => __('Site Description')
        ));
        
        $this->addElement('text', 'administrator_email', array(
            'label' => __('Administrator Email'),
            'validators' => array('EmailAddress'), 
            'required' => true, 
        ));
        
        $this->addElement('text', 'copyright', array(
            'label' => __('Site Copyright Information')
        ));
        
        $this->addElement('text', 'author', array(
            'label' => __('Site Author Information')
        ));
        
        $this->addElement('text', 'tag_delimiter', array(
            'label' => __('Tag Delimiter'),
            'description' => __('Separate tags using this character or string.'),
            'value' => self::DEFAULT_TAG_DELIMITER, 
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
            'label' => __('Fullsize Image Size'),
            'description' => __('Maximum fullsize image size constraint (in pixels)'), 
            'value' => self::DEFAULT_FULLSIZE_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'thumbnail_constraint', array(
            'label' => __('Thumbnail Size'),
            'description' => __('Maximum thumbnail size constraint (in pixels)'), 
            'value' => self::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'square_thumbnail_constraint', array(
            'label' => __('Square Thumbnail Size'),
            'description' => __('Maximum square thumbnail size constraint (in pixels)'), 
            'value' => self::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_admin', array(
            'label' => __('Items Per Page (admin)'), 
            'description' => __('Limit the number of items displayed per page in the administrative interface.'), 
            'value' => self::DEFAULT_PER_PAGE_ADMIN, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_public', array(
            'label' => __('Items Per Page (public)'), 
            'description' => __('Limit the number of items displayed per page in the public interface.'), 
            'value' => self::DEFAULT_PER_PAGE_PUBLIC, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('checkbox', 'show_empty_elements', array(
            'label' => __('Show Empty Elements'),
            'class' => 'checkbox',
            'description' => __('Check box to show metadata elements with no text.')
        ));
        
        $this->addElement('text', 'path_to_convert', array(
            'label' => __('ImageMagick Directory Path')
        ));
        
        $this->addElement('submit', 'install_submit', array(
            'label' => __('Install'),
            'decorators' => array('Tooltip', 'ViewHelper')
        ));
        
        $this->addDisplayGroup(
            array('username', 'password', 'password_confirm', 'super_email'), 
            'superuser_account', 
            array('legend' => __('Default Superuser Account'))
        );
        
        $this->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'tag_delimiter', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements', 
                  'path_to_convert'), 
            'site_settings', 
            array('legend' =>__('Site Settings'))
        );
        
        $this->addDisplayGroup(
            array('install_submit'), 
            'submit'
        );
        
    }
}
