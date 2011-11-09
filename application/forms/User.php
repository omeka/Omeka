<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Edit form for Omeka users.
 *
 * @package Omeka
 */
class Omeka_Form_User extends Omeka_Form
{    
    const ERROR_USERNAME_TAKEN = "This username is already in use.  Please choose another.";
    const ERROR_USERNAME_REQUIRED = "Username is required.";
    const ERROR_USERNAME_ALNUM = "Username must consist only of letters and numbers (alphanumeric).";
    const ERROR_USERNAME_TOO_SHORT = "Username must be more than %min% characters long.";
    const ERROR_USERNAME_TOO_LONG = "Username must be shorter than %max% characters.";
    const ERROR_EMAIL_REQUIRED = "Email is required.";
    const ERROR_EMAIL_TAKEN = "This email address is already in use.  Please choose another.";
    const ERROR_EMAIL_NOT_MATCH = "Emails do not match.";
    const ERROR_EMAIL_INVALID = "This email address is not valid.  Please provide a valid email address.";
    const ERROR_PASSWORD_NOT_MATCH = "Passwords do not match.";
    const ERROR_PASSWORD_TOO_SHORT = "Passwords must be at least %min% characters long.";
    const ERROR_PASSWORD_CONFIRM_REQUIRED = "Please re-type your password to confirm.";
    const ERROR_PASSWORD_REQUIRED = "Please enter a password.";
    const ERROR_TERMS_OF_SERVICE = 'New users must agree to the Terms of Service.';
    const ERROR_FIRST_NAME_REQUIRED = "First name is required.";
    const ERROR_LAST_NAME_REQUIRED = "Last name is required.";     
    
    
    private $_hasRoleElement;
    
    private $_hasActiveElement;
    
    private $_submitButtonText;
    
    private $_user;
    
    public function init()
    {
        parent::init();
        
        $this->addElement('text', 'username', array(
            'label'         => 'Username',
            'required'      => true,
            'size'          => '30',
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => 
                    array(
                        'messages' => array(
                            'isEmpty' => self::ERROR_USERNAME_REQUIRED
                        )
                    )
                ),
                array('validator' => 'Alnum', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_Alnum::INVALID => self::ERROR_USERNAME_ALNUM,
                            Zend_Validate_Alnum::NOT_ALNUM => self::ERROR_USERNAME_ALNUM
                        )
                    )
                ),
                array('validator' => 'StringLength', 'breakChainOnFailure' => true, 'options' => 
                    array(
                        'min' => User::USERNAME_MIN_LENGTH,
                        'max' => User::USERNAME_MAX_LENGTH,
                        'messages' => array(
                            Zend_Validate_StringLength::TOO_SHORT => self::ERROR_USERNAME_TOO_SHORT,
                            Zend_Validate_StringLength::TOO_LONG => self::ERROR_USERNAME_TOO_LONG
                        )
                    )
                ),
                array('validator' => 'Db_NoRecordExists', 'options' => 
                    array(
                        'table'     =>  $this->_user->getTable()->getTableName(), 
                        'field'     =>  'username',
                        'exclude'   =>  array(
                            'field' => 'id',
                            'value' => (int)$this->_user->id
                        ),
                        'adapter'   =>  $this->_user->getDb()->getAdapter(), 
                        'messages'  =>  array(
                            'recordFound' => self::ERROR_USERNAME_TAKEN
                        )
                    )
                )    
            ),
            
        ));
        
        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => self::ERROR_FIRST_NAME_REQUIRED,
                        Zend_Validate_NotEmpty::INVALID => self::ERROR_FIRST_NAME_REQUIRED
                    )
                ))
            )
        ));
        
        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'size'  => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => self::ERROR_LAST_NAME_REQUIRED,
                        Zend_Validate_NotEmpty::INVALID => self::ERROR_LAST_NAME_REQUIRED
                    )
                ))
            )
        ));
        
        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => self::ERROR_EMAIL_REQUIRED,
                        Zend_Validate_NotEmpty::INVALID => self::ERROR_EMAIL_REQUIRED
                    )
                )),
                array('validator' => 'EmailAddress', 'options' => array(
                    'messages' => array(
                        Zend_Validate_EmailAddress::INVALID  => self::ERROR_EMAIL_INVALID,
                        Zend_Validate_EmailAddress::INVALID_FORMAT => self::ERROR_EMAIL_INVALID,
                        Zend_Validate_EmailAddress::INVALID_HOSTNAME => self::ERROR_EMAIL_INVALID
                    )
                )),
                array('validator' => 'Db_NoRecordExists', 'options' => array(
                    'table'     =>  $this->_user->getDb()->Entity, 
                    'field'     =>  'email',
                    'exclude'   =>  array(
                        'field' => 'email',
                        'value' => (string)$this->_user->email
                    ),
                    'adapter'   =>  $this->_user->getDb()->getAdapter(), 
                    'messages'  =>  array(
                        'recordFound' => self::ERROR_EMAIL_TAKEN
                    )
                )),
            )
        ));
        
        $this->addElement('text', 'institution', array(
            'label' => 'Institution',
            'size' => '30'
        ));
        
        if ($this->_hasRoleElement) {
            $this->addElement('select', 'role', array(
                'label' => 'Role',
                'multiOptions' => get_user_roles(),
                'required' => true
            ));
        }
        
        if ($this->_hasActiveElement) {
            $this->addElement('radio', 'active', array( // Radioactive, get it? HARR
                'label' => 'Status',
                'multiOptions' => array('0'=>'Inactive','1'=>'Active'),
                'required' => true,
                'value' => '0'
            ));
        }
        
        $this->addElement('submit', 'submit', array(
            'label' => $this->_submitButtonText
        ));
    }
    
    public function setHasRoleElement($flag)
    {
        $this->_hasRoleElement = (boolean)$flag;
    }
        
    public function setHasActiveElement($flag)
    {
        $this->_hasActiveElement = (boolean)$flag;
    }
    
    public function setSubmitButtonText($text)
    {
        if (!$this->getElement('submit')) {
            $this->_submitButtonText = $text;
        } else {
            $this->submit->setLabel($text);
        }
    }   
    
    public function setUser(User $user)
    {
        $this->_user = $user;
    }
}
