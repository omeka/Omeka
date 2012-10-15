<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A simple form to enable a user to recover/reset their password.
 * 
 * Contains only an 'email' input and a submit button.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_RecoverPassword extends Omeka_Form
{
    private $_db;
    
    public function init()
    {
        parent::init();
        
        $this->setAttrib('id', 'recover-password');
        
        $this->addElement('text', 'email', array(
            'label' => __('Email'),
            'required' => true,
            'validators' => array(
                array(
                    'validator' => 'NotEmpty', 
                    'breakChainOnFailure' => true, 
                    'options' => array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => 
                                __('Email address is required.')
                        )
                    )
                ),
                array(
                    'validator' => 'EmailAddress', 
                    'breakChainOnFailure' => true,
                    'options' => array(
                        'messages' => array(
                            Zend_Validate_EmailAddress::INVALID =>
                                __('Invalid email address given.'),
                            Zend_Validate_EmailAddress::INVALID_FORMAT => 
                                __('Invalid format given for email address.'),
                            Zend_Validate_EmailAddress::INVALID_HOSTNAME   => 
                                __('Invalid hostname given for email address.'),
                            //Zend_Validate_EmailAddress::INVALID_SEGMENT => '',
                            //Zend_Validate_EmailAddress::DOT_ATOM => '',
                            //Zend_Validate_EmailAddress::QUOTED_STRING => '', 
                            //Zend_Validate_EmailAddress::INVALID_LOCAL_PART => '', 
                            //Zend_Validate_EmailAddress::LENGTH_EXCEEDED => '',
                        )
                    )
                ),
                array(
                    'validator' => 'Db_RecordExists', 
                    'options' => array(
                        'table' => $this->_db->User,
                        'field' => 'email',
                        'adapter' => $this->_db->getAdapter(),
                        'messages' => array(
                            'noRecordFound' => __("Invalid email address")
                        )
                    )
                )    
            )
        ));
        
        $this->addElement('submit', 'Submit');
    }
    
    public function setDb(Omeka_Db $db)
    {
        $this->_db = $db;
    }
}
