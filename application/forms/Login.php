<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Forms
 */

/**
 * 
 *
 * @package Omeka
 * @subpackage Forms
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Form_Login extends Omeka_Form
{
    public function init()
    {
        parent::init();
        
        $this->setMethod('post');
        $this->setAttrib('id', 'login-form');
        $this->addElement('text', 'username', array(
            'label'     => __('Username'),
            'required'  => true,
            'class' => 'textinput',
            'validators' => array(
                    array('validator'   => 'NotEmpty',
                          'options'     =>  array(
                              'messages' => array(
                                  'isEmpty' => __('Username cannot be empty.')))))));
        $this->addElement('password', 'password', array(
            'label'=>__('Password'),
            'required'  => true,
            'class' => 'textinput',
            'validators' => array(
                    array('validator'   => 'NotEmpty',
                          'options'     =>  array(
                              'messages' => array(
                                  'isEmpty' => __('Password cannot be empty.')))))));
        $this->addElement('checkbox', 'remember', array('class'=>'checkbox', 'label'=>__('Remember Me?')));
        $this->addDisplayGroup(array('username','password','remember'), 'login');
        $this->addElement('submit', 'submit', array('label' => __('Log In')));
    }
}
