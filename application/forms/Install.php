<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 **/

/**
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Form_Install extends Omeka_Form
{
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    const DEFAULT_SHOW_EMPTY_ELEMENTS = true;
    const DEFAULT_USER_FIRST_NAME = 'Super';
    const DEFAULT_USER_LAST_NAME = 'User';
        
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
            
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'class' => 'textinput', 
            'validators' => array(array('StringLength', false, array(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH))), 
            'required' => true
        ));
        
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'description' => 'Password for your super user, 6–40 characters.', 
            'validators' => array(array('StringLength', false, array(User::PASSWORD_MIN_LENGTH))), 
            'required' => true
        ));
        
        $this->addElement('text', 'super_email', array(
            'label' => 'Email',
            'validators' => array('EmailAddress'), 
            'required' => true, 
        ));
        
        $this->addElement('text', 'site_title', array(
            'label' => 'Site Title',
            'required' => true
        ));
        
        $this->addElement('textarea', 'description', array(
            'label' => 'Site Description'
        ));
        
        $this->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email',
            'validators' => array('EmailAddress'), 
            'required' => true, 
        ));
        
        $this->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information'
        ));
        
        $this->addElement('text', 'author', array(
            'label' => 'Site Author Information'
        ));
        
        $this->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size',
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'value' => self::DEFAULT_FULLSIZE_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size',
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'value' => self::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size',
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'value' => self::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'value' => self::DEFAULT_PER_PAGE_ADMIN, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'value' => self::DEFAULT_PER_PAGE_PUBLIC, 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $this->addElement('checkbox', 'show_empty_elements', array(
            'label' => 'Show Empty Elements',
            'class' => 'checkbox',
            'description' => 'Whether metadata elements with no text will be displayed.'
        ));
        
        $this->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path'
        ));
        
        $this->addElement('submit', 'install_submit', array(
            'label' => 'Install',
            'decorators' => array('Tooltip', 'ViewHelper')
        ));
        
        $this->addDisplayGroup(
            array('username', 'password', 'super_email'), 
            'superuser_account', 
            array('legend' => 'Default Superuser Account')
        );
        
        $this->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements', 
                  'path_to_convert'), 
            'site_settings', 
            array('legend' =>'Site Settings')
        );
        
        $this->addDisplayGroup(
            array('install_submit'), 
            'submit'
        );
        
    }
}
