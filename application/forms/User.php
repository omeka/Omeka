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
    private $_hasRoleElement;
    
    private $_hasActiveElement;
    
    private $_submitButtonText;
    
    private $_user;
    
    public function init()
    {
        parent::init();
        
        $this->addElement('text', 'username', array(
            'label'         => __('Username'),
            'required'      => true,
            'size'          => '30',
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => 
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Username is required.')
                        )
                    )
                ),
                array('validator' => 'Alnum', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_Alnum::NOT_ALNUM =>
                                __('Username must contain only letters and numbers.')
                        )
                    )
                ),
                array('validator' => 'StringLength', 'breakChainOnFailure' => true, 'options' => 
                    array(
                        'min' => User::USERNAME_MIN_LENGTH,
                        'max' => User::USERNAME_MAX_LENGTH,
                        'messages' => array(
                            Zend_Validate_StringLength::TOO_SHORT =>
                                __('Username must be at least %min% characters long.'),
                            Zend_Validate_StringLength::TOO_LONG =>
                                __('Username must be at most %max% characters long.')
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
                            'recordFound' => __('This username is already in use.')
                        )
                    )
                )    
            ),
            
        ));
        
        $this->addElement('text', 'first_name', array(
            'label' => __('First Name'),
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => __('First name is required.')
                    )
                ))
            )
        ));
        
        $this->addElement('text', 'last_name', array(
            'label' => __('Last Name'),
            'size'  => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => __('Last name is required.')
                    )
                ))
            )
        ));

        $invalidEmailMessage = __('This email address is invalid.');
        $this->addElement('text', 'email', array(
            'label' => __('Email'),
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => __('Email is required.')
                    )
                )),
                array('validator' => 'EmailAddress', 'options' => array(
                    'messages' => array(
                        Zend_Validate_EmailAddress::INVALID  => $invalidEmailMessage,
                        Zend_Validate_EmailAddress::INVALID_FORMAT => $invalidEmailMessage,
                        Zend_Validate_EmailAddress::INVALID_HOSTNAME => $invalidEmailMessage
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
                        'recordFound' => __('This email address is already in use.')
                    )
                )),
            )
        ));
        
        $this->addElement('text', 'institution', array(
            'label' => __('Institution'),
            'size' => '30'
        ));
        
        if ($this->_hasRoleElement) {
            $this->addElement('select', 'role', array(
                'label' => __('Role'),
                'multiOptions' => get_user_roles(),
                'required' => true
            ));
        }
        
        if ($this->_hasActiveElement) {
            $this->addElement('checkbox', 'active', array(
                'label' => __('Active?')
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
