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

    public function init()
    {
        parent::init();

        $this->setAttrib('id', 'change-password');
        $this->addElement('password', 'current_password',
            [
                'label' => __('Current Password'),
                'description' => __('Password must be at least 6 characters long.'),
                'required' => true,
                'errorMessages' => [__("Invalid current password")],
            ]
        );

        $this->addElement('password', 'new_password',
            [
                'label' => __('New Password'),
                'required' => true,
                'description' => __('Password must be at least 6 characters long.'),
                'validators' => [
                    ['validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                        [
                            'messages' => [
                                'isEmpty' => __("New password must be entered.")
                            ]
                        ]
                    ],
                    [
                        'validator' => 'Confirmation',
                        'options' => [
                            'field' => 'new_password_confirm',
                            'messages' => [
                                Omeka_Validate_Confirmation::NOT_MATCH => __('New password must be typed correctly twice.')
                            ]
                         ]
                    ],
                    [
                        'validator' => 'StringLength',
                        'options' => [
                            'min' => User::PASSWORD_MIN_LENGTH,
                            'messages' => [
                                Zend_Validate_StringLength::TOO_SHORT => __("New password must be at least %min% characters long.")
                            ]
                        ]
                    ]
                ]
            ]
        );
        $this->addElement('password', 'new_password_confirm',
            [
                'label' => __('Repeat New Password'),
                'required' => true,
                'errorMessages' => [__('New password must be typed correctly twice.')]
            ]
        );

        $this->addElement('hash', 'password_csrf');

        $this->addDisplayGroup(['current_password',
                                     'new_password',
                                     'new_password_confirm'],
                               'change_password',
                               ["legend" => __("Change Password")]);
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->current_password->addValidator(new Omeka_Validate_UserPassword($this->_user));
    }
}
