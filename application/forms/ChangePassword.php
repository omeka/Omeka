<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Form for changing a user's password.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_ChangePassword extends Omeka_Form
{
    private $_user;
    
    const ERROR_CURRENT_PASSWORD_INVALID = "Invalid current password.";
    const ERROR_NEW_PASSWORD_REQUIRED = "New password must be entered.";
    const ERROR_NEW_PASSWORD_CONFIRM_REQUIRED = 'New password must be typed correctly twice.';
    const ERROR_NEW_PASSWORD_TOO_SHORT = "New password must be at least %min% characters long.";
    
    public function init()
    {
        parent::init();
        
        $this->setAttrib('id', 'change-password');
        $this->addElement('password', 'current_password',
            array(
                'label'         => __('Current Password'),
                'required'      => true,
                'errorMessages' => array(self::ERROR_CURRENT_PASSWORD_INVALID),
            )
        );
        
        $this->addElement('password', 'new_password',
            array(
                'label'         => __('New Password'),
                'required'      => true,
                'validators'    => array(
                    array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => 
                        array(
                            'messages' => array(
                                'isEmpty' => self::ERROR_NEW_PASSWORD_REQUIRED
                            )
                        )
                    ),
                    array(
                        'validator' => 'Confirmation', 
                        'options'   => array(
                            'field'     => 'new_password_confirm',
                            'messages'  => array(
                                Omeka_Validate_Confirmation::NOT_MATCH => self::ERROR_NEW_PASSWORD_CONFIRM_REQUIRED
                            )
                         )
                    ),
                    array(
                        'validator' => 'StringLength',
                        'options'   => array(
                            'min' => User::PASSWORD_MIN_LENGTH,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_SHORT => self::ERROR_NEW_PASSWORD_TOO_SHORT
                            )
                        )
                    )
                )
            )
        );
        $this->addElement('password', 'new_password_confirm',
            array(
                'label'         => __('Repeat New Password'),
                'required'      => true,
                'errorMessages' => array(self::ERROR_NEW_PASSWORD_CONFIRM_REQUIRED)
            )
        );
        $this->addElement('submit', 'submit',
            array(
                'label'         => __('Save Password')
            )
        );
        
        $this->addDisplayGroup(array('current_password', 
                                     'new_password', 
                                     'new_password_confirm', 
                                     'submit'), 
                               'change_password', 
                               array("legend" => __("Change Password")));
    }
    
    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->current_password->addValidator(new Omeka_Validate_UserPassword($this->_user));
    }
}
