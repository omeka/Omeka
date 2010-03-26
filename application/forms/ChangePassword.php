<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Form for changing a user's password.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Form_ChangePassword extends Omeka_Form
{
    private $_user;
    
    public function init()
    {
        parent::init();
                        
        $this->addElement('text', 'current_password', 
            array(
                'label'         => 'Current Password',
                'required'      => true,
                'class'         => 'textinput',
                'errorMessages' => array('Invalid current password'),
            )
        );
        
        $this->addElement('text', 'new_password',
            array(
                'label'         => 'New Password',
                'required'      => true,
                'class'         => 'textinput',
                'validators'    => array(
                    array('Confirmation', null, array('new_password_confirm')),
                    array('StringLength', null, array(User::PASSWORD_MIN_LENGTH))
                )
            )
        );
        $this->addElement('text', 'new_password_confirm',
            array(
                'label'         => 'Repeat New Password',
                'required'      => true,
                'class'         => 'textinput',
                'errorMessages' => array('New password must be typed correctly twice.')
            )
        );
        $this->addElement('submit', 'submit',
            array(
                'label'         => 'Save Password',
                'class'         => 'submit submit-medium'
            )
        );
        
        $this->addDisplayGroup(array('current_password', 
                                     'new_password', 
                                     'new_password_confirm', 
                                     'submit'), 
                               'change_password', 
                               array("legend" => "Change Password"));
    }
    
    public function setUser(User $user)
    {
        $this->_user = $user;
        
        // Super users don't need to know the original password.
        if ($this->_user->role == 'super') {
            $this->removeElement('current_password');
        } else {
            $this->current_password->addValidator(new Omeka_Validate_UserPassword($this->_user));
        }
    }
}