<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Forms
 */

/**
 * User metadata form.
 *
 * @package Omeka
 * @subpackage Forms
 */
class Omeka_Form_User extends Omeka_Form
{
    private $_user;
    
    public function init()
    {
        parent::init();

        $user = $this->getUser();
        
        $this->setMethod('post');
        $this->addElement('text', 'username', array(
            'label'         => 'Username',
            'required'      => true,
            'class'         => 'textinput',
            'description'   => 'Only alphanumeric characters are allowed.',
            'size'          => 30,
            'validators'    => array(
                array('StringLength', false, array(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH)),
                'Alnum')
        ));
        $this->addElement('text', 'first_name', array(
            'label'         => 'First Name',
            'class'         => 'textinput',
            'size'          => 30
        ));
        $this->addElement('text', 'last_name', array(
            'label'         => 'Last Name',
            'class'         => 'textinput',
            'size'          => 30
        ));
        $this->addElement('text', 'email', array(
            'label'         => 'Email',
            'required'      => true,
            'class'         => 'textinput',
            'size'          => 30,
            'validators'    => array('EmailAddress')
        ));
        $this->addElement('text', 'institution', array(
            'label'         => 'Institution',
            'class'         => 'textinput',
            'size'          => 30
        ));
        if (has_permission($user, 'change-role')) {
            $this->addElement('select', 'role', array(
                'label'        => 'Role',
                'required'     => true,
                'multiOptions' => array('' => 'Select a Role') + get_user_roles()
            ));
        }
        // We shouldn't be able to set active/inactive if the user's
        // being created.
        if ($user->exists() && has_permission($user, 'change-status')) {
            $this->addElement('checkbox', 'active', array(
                'label'       => 'Active',
                'description' => 'Uncheck this box to prevent this user from logging in.'
            ));
        }

        $this->_setDefaultValues($user);

        $this->addDisplayGroup($this->getElements(), 'user-info');

        $submitLabel = $user->exists() ? 'Save Changes' : 'Add this User'; 
        $this->addElement('submit', 'submit', array('label' => $submitLabel));
    }

    /**
     * Set the value for each form element to the value from the User
     * object.
     *
     * @param User $user
     */
    private function _setDefaultValues(User $user)
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $name = $element->getName();
            $userValue = @$user->$name;
            if (isset($userValue)) {
                $element->setValue($userValue);
            }
        }
    }

    /**
     * Set the User object this form will operate on.
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Get the User object set on the form.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }
}
