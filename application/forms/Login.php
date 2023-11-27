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

        $decorators = array(
            'ViewHelper',
            array('Errors', array('class' => 'error')),
            array(array('input' => 'HtmlTag'), array('tag' => 'div', 'class' => 'inputs')),
            array('Label', array('tag' => 'div', 'tagClass' => 'field-meta')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'field'))
        );

        $this->setMethod('post');
        $this->setAttrib('id', 'login-form');
        $this->addElement('text', 'username', array(
            'label' => __('Username'),
            'required' => true,
            'validators' => array(
                    array('validator' => 'NotEmpty',
                          'options' => array(
                              'messages' => array(
                                  'isEmpty' => __('Username cannot be empty.'))))),
            'decorators' => $decorators,
        ));
        $this->addElement('password', 'password', array(
            'label' => __('Password'),
            'required' => true,
            'validators' => array(
                    array('validator' => 'NotEmpty',
                          'options' => array(
                              'messages' => array(
                                  'isEmpty' => __('Password cannot be empty.'))))),
            'decorators' => $decorators,
        ));
        $this->addElement('checkbox', 'remember', array(
            'class' => 'checkbox',
            'label' => __('Remember Me?'),
            'decorators' => $decorators,
        ));
        $this->addDisplayGroup(array('username', 'password', 'remember'), 'login');
        $this->addElement('submit', 'submit', array('label' => __('Log In')));
    }
}
