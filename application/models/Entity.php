<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @deprecated
 */
class Entity extends Omeka_Record
{
    public $first_name;
    public $middle_name;
    public $last_name;
    public $email;
    public $institution;
            
    protected $_related = array('name'        => 'getName', 
                                'User'        => 'getUser');
            
    /**
     * These are all the things that will cause saving an entity to fault
     *     1) blank first & last name & institution name
     *     2) invalid email address
     */
    protected function _validate()
    {        
        if (!empty($this->email) && !Zend_Validate::is($this->email, 'EmailAddress')) {
            $this->addError('email', __('The email address provided is not valid.'));
        }
                
        if (empty($this->first_name) && empty($this->last_name) and empty($this->institution)) {
            $this->addError('Name', __('The name for a person may not be completely blank.'));
        }
    }
    
    /**
     * Trim all the data that comes in via the form.
     *
     * @return array
     */
    protected function filterInput($input)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');
        
        $filters = array('first_name'  => 'StringTrim', 
                         'middle_name' => 'StringTrim', 
                         'last_name'   => 'StringTrim', 
                         'email'       => 'StringTrim', 
                         'institution' => 'StringTrim');
            
        $filter = new Zend_Filter_Input($filters, null, $input, $options);

        $clean = $filter->getUnescaped();

        return $clean;
    }
    
    /**
     * Combine the first, middle and last name fields to produce a full name
     * string.
     *
     * @return string
     */
    public function getName() {
        return implode(' ', array($this->first_name, $this->middle_name, $this->last_name));
    }
    
    /**
     * Get the User record this entity is associated with.
     *
     * @return User
     */
    public function getUser()
    {
        $id = (int) $this->id;
        return $this->getDb()->getTable('User')->findByEntity($id);
    }
    
    /**
     * When deleting an entity, there is much else to be done.
     * 1) Delete any associated user account
     * 2) Delete all taggings associated with this entity
     * 3) Update the entities_relations table so that every reference to this 
     *    entity are NULLed
     *
     * @return void
     */
    protected function _delete()
    {        
        $id = (int) $this->id;
        
        //Check if there is a user account associated with this
        
        if ($user = $this->User) {
            $user->delete();
        }
        
        $db = $this->getDb();
        
        //Remove all taggings associated with this entity
        $taggings = $db->getTable('Taggings')->findBy(array('entity' => $id));
        
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
        
        $update_join = "
        UPDATE $db->EntitiesRelations 
        SET entity_id = NULL 
        WHERE entity_id = ?";
        
        $db->exec($update_join, array($id));        
    }
    
    /**
     * This will merge $entity with $this, where $this is the record that 
     * remains in the db (presumably as the actor it takes precedence)
     *
     * @return bool
     */
    public function merge($entity)
    {
        try {
            if (!$this->exists() or !$entity->exists()) {
                throw new Omeka_Record_Exception( __('Both of these Entities must be persistent in order to merge them.') );
            }
            
            $db = $this->getDb();
            
            // These are the classes where foreign keys will be affected
            $joinClasses = array('EntitiesRelations'=>'entity_id', 
                                 'User'=>'entity_id');            
                    
            // Sql statement to update the join tables
            foreach ($joinClasses as $jc => $fk) {
                $jt = $db->$jc;
                $sql = "
                UPDATE $jt j 
                SET j.$fk = ? 
                WHERE j.$fk = ?";
                $db->exec($sql, array($this->id, $entity->id));
            }
            $entity->delete();
            return true;
            
        } catch (Exception $e) {
            Zend_Debug::dump( $e );exit;
        }
    }
}
