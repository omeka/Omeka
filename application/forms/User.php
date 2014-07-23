<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Edit form for Omeka users.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_User extends Omeka_Form
{
    private $_hasRoleElement;
    
    private $_hasActiveElement;
    
    private $_user;
    
    private $_usersActivations;
    
    public function init()
    {
        parent::init();
        
        $this->addElement('text', 'username', array(
            'label'         => __('Username'),
            'description'   => __('Username must be 30 characters or fewer. Whitespace is not allowed.'),
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
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '#^[a-zA-Z0-9.*@+!\-_%\#\^&$]*$#u',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH =>
                                __('Whitespace is not allowed. Only these special characters may be used: %s', ' + ! @ # $ % ^ & * . - _' )
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
        
        $this->addElement('text', 'name', array(
            'label' => __('Display Name'),
            'description' => __('Name as it should be displayed on the site'),
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => __('Real Name is required.')
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
                    'table'     =>  $this->_user->getTable()->getTableName(), 
                    'field'     =>  'email',
                    'exclude'   =>  array(
                        'field' => 'id',
                        'value' => (int)$this->_user->id
                    ),
                    'adapter'   =>  $this->_user->getDb()->getAdapter(), 
                    'messages'  =>  array(
                        'recordFound' => __('This email address is already in use.')
                    )
                )),
            )
        ));
        
        if ($this->_hasRoleElement) {
            $this->addElement('select', 'role', array(
                'label' => __('Role'),
                'description' => __("Roles describe the permissions a user has. See <a href='http://omeka.org/codex/User_Roles' target='_blank'>documentation</a> for details."),
                'multiOptions' => get_user_roles(),
                'required' => true
            ));
        }

        if ($this->_hasActiveElement) {
            $description = __('Inactive users cannot log in to the site.');
            if( ($this->_user->active == 0) && ($this->_usersActivations)) {
                $description .= '<br>' . __('Activation has been pending since %s.', format_date($this->_usersActivations->added));
            }
            $this->addElement('checkbox', 'active', array(
                'label' => __('Active?'),
                'description' => $description 
            ));
        }

        $this->addElement('hash', 'user_csrf', array(
            'timeout' => 3600
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

    public function setUser(User $user)
    {
        $this->_user = $user;
    }
    
    public function setUsersActivations($ua)
    {
        $this->_usersActivations = $ua;
    }
}
