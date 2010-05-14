<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * A simple form to enable a user to recover/reset their password.
 * 
 * Contains only an 'email' input and a submit button.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Form_RecoverPassword extends Omeka_Form
{
    private $_db;
    
    public function init()
    {
        parent::init();
        
        $this->setAttrib('id', 'recover-password');
        
        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' => array(
                    'messages' => array(
                        'isEmpty' => 'Email address is required.'
                    )
                )),
                array('validator' => 'EmailAddress', 'breakChainOnFailure' => true),
                array('validator' => 'Db_RecordExists', 'options' => array(
                    'table' => $this->_db->Entity,
                    'field' => 'email',
                    // Exclude the email addresses that don't correspond with user accounts.
                    'exclude' => $this->_getExcludeClause(),
                    'adapter' => $this->_db->getAdapter(),
                    'messages' => array(
                        'noRecordFound' => "Invalid email address"
                    )
                ))    
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
