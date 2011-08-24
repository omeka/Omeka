<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Forms
 * @access private
 */

/**
 * A simple form to enable a user to recover/reset their password.
 * 
 * Contains only an 'email' input and a submit button.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Forms
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
                        'table' => $this->_db->Entity,
                        'field' => 'email',
                        // Exclude the email addresses that don't correspond with user accounts.
                        'exclude' => $this->_getExcludeClause(),
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
    
    private function _getExcludeClause()
    {
        return "{$this->_db->Entity}.email IN (SELECT e.email FROM {$this->_db->Entity} e INNER JOIN {$this->_db->User} u ON u.entity_id = e.id)";
    }
}
