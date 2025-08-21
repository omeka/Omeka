<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Form for allowing a user to reset his/her password.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_ResetPassword extends Omeka_Form
{
    public function init()
    {
        parent::init();

        $this->setAttrib('id', 'reset-password');

        $this->addElement('password', 'new_password', [
            'label' => __('Create a Password'),
            'validators' => [
                ['validator' => 'NotEmpty', 'options' => [
                    'messages' => [
                        'isEmpty' => __('Password is required.')
                    ]
                ]],
                ['validator' => 'Confirmation', 'options' => [
                    'field' => 'new_password_confirm',
                    'messages' => [
                        'notMatch' => __("Typed passwords do not match.")]
                ]],
                ['validator' => 'StringLength', 'options' => [
                    'min' => User::PASSWORD_MIN_LENGTH,
                    'messages' => [
                        'stringLengthTooShort' => "Password must be at least %min% characters in length."]
                ]]
            ]
        ]);

        $this->addElement('password', 'new_password_confirm', [
            'label' => __('Re-type the Password')
        ]);

        $this->addElement('submit', 'Submit');
    }
}
