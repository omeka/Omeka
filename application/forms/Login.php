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
class Omeka_Form_Login extends Omeka_Form
{
    public function init()
    {
        parent::init();

        $decorators = [
            'ViewHelper',
            ['Errors', ['class' => 'error']],
            [['input' => 'HtmlTag'], ['tag' => 'div', 'class' => 'inputs']],
            ['Label', ['tag' => 'div', 'tagClass' => 'field-meta']],
            ['HtmlTag', ['tag' => 'div', 'class' => 'field']]
        ];

        $this->setMethod('post');
        $this->setAttrib('id', 'login-form');
        $this->addElement('text', 'username', [
            'label' => __('Username'),
            'required' => true,
            'validators' => [
                    ['validator' => 'NotEmpty',
                          'options' => [
                              'messages' => [
                                  'isEmpty' => __('Username cannot be empty.')]]]],
            'decorators' => $decorators,
        ]);
        $this->addElement('password', 'password', [
            'label' => __('Password'),
            'required' => true,
            'validators' => [
                    ['validator' => 'NotEmpty',
                          'options' => [
                              'messages' => [
                                  'isEmpty' => __('Password cannot be empty.')]]]],
            'decorators' => $decorators,
        ]);
        $this->addElement('checkbox', 'remember', [
            'class' => 'checkbox',
            'label' => __('Remember Me?'),
            'decorators' => $decorators,
        ]);
        $this->addDisplayGroup(['username', 'password', 'remember'], 'login');
        $this->addElement('submit', 'submit', ['label' => __('Log In')]);
    }
}
