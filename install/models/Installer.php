<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Installer/Requirements.php';

/*
-- Checkbox next to "Administrator Email": "Use Superuser Email"
-- Runtime JavaScript validation
*/
class Installer
{
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    
    private $_db;
    private $_form;
    
    public function __construct(Omeka_Db $db, Installer_Requirements $requirements)
    {   
        $this->_requirements = $requirements;
        
        // Set the database object;
        $this->_db = $db;
        //die($this->_db->prefix);
        $this->setForm();
    }
    
    public function checkRequirements()
    {
        $this->_requirements->check();
    }
    
    public function hasError()
    {
        return (boolean)count($this->getErrorMessages());
    }
    
    public function hasWarning()
    {
        return (boolean)count($this->getWarningMessages());
    }
    
    public function getErrorMessages()
    {
        return $this->_requirements->getErrorMessages();
    }
    
    public function getWarningMessages()
    {
        return $this->_requirements->getWarningMessages();
    }
    
    public function setForm()
    {
        // http://framework.zend.com/manual/en/zend.form.quickstart.html
        // http://devzone.zend.com/article/3450
        $form = new Zend_Form;
        $form->setAction('index.php')->setMethod('post');
        $form->removeDecorator('HtmlTag');
        
        // Add form elements.
        $elementDecorators = array('ViewHelper', 
                                   'Errors', 
                                   'Description',
                                   'Label', 
                                   array('HtmlTag', array('tag' => 'div')));
        
        $form->addElement('text', 'username', array(
            'label' => 'Username',
            'class' => 'textinput', 
            'validators' => array(array('StringLength', false, array(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH))), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('password', 'password', array(
            'label' => 'Password',
            'description' => 'Password for your super user, 6–40 characters.', 
            'class' => 'textinput', 
            'validators' => array(array('StringLength', false, array(User::PASSWORD_MIN_LENGTH))), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'super_email', array(
            'label' => 'Email',
            'class' => 'textinput', 
            'validators' => array('EmailAddress'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'site_title', array(
            'label' => 'Site Title',
            'class' => 'textinput',
            'class' => 'textinput', 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('textarea', 'description', array(
            'label' => 'Site Description',
            'class' => 'textinput', 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email',
            'class' => 'textinput', 
            'validators' => array('EmailAddress'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information',
            'class' => 'textinput', 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'author', array(
            'label' => 'Site Author Information',
            'class' => 'textinput', 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size',
            'class' => 'textinput', 
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'value' => self::DEFAULT_FULLSIZE_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size',
            'class' => 'textinput', 
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'value' => self::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size',
            'class' => 'textinput', 
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'value' => self::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'class' => 'textinput',
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'value' => self::DEFAULT_PER_PAGE_ADMIN, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'class' => 'textinput',
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'value' => self::DEFAULT_PER_PAGE_PUBLIC, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('checkbox', 'show_empty_elements', array(
            'label' => 'Show Empty Elements',
            'class' => 'checkbox',
            'description' => 'Whether metadata elements with no text will be displayed.',
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path',
            'class' => 'textinput', 
            'value' => $this->_getPathToConvert(), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('submit', 'install_submit', array(
            'label' => 'Install',
            'class' => 'textinput', 
            'decorators' => array('Tooltip', 'ViewHelper')
        ));

        // Add fieldsets.
        $displayGroupDecorators = array('FormElements', 'Fieldset');
        
        $form->addDisplayGroup(
            array('username', 'password', 'super_email'), 
            'superuser_account', 
            array('legend' => 'Default Superuser Account', 
                  'decorators' => $displayGroupDecorators)
        );
        
        $form->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements', 
                  'path_to_convert'), 
            'site_settings', 
            array('legend' =>'Site Settings', 
                  'decorators' => $displayGroupDecorators)
        );
        
        $form->addDisplayGroup(
            array('install_submit'), 
            'submit', 
            array('decorators' => $displayGroupDecorators)
        );
        
        $form->setView(new Zend_View);
        $this->_form = $form;
    }
    
    public function getForm()
    {
        return $this->_form;
    }
    
    /**
     * @param array $values Set of values required by the installer.  Usually
     * passed in via the form.
     * @param boolean $createUser Whether or not to create a new user along with
     * this installation.  Defaults to true.
     */
    public function install(array $values, $createUser = true)
    {
        $db = $this->_db;
        
        // Create the database tables and insert default data.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $this->_db->query($sql)->fetchAll();
        if (empty($tables)) {
            include INSTALL_DIR . DIRECTORY_SEPARATOR . 'install.sql.php';
            $this->_db->execBlock($installSql);
        }
        
        if ($createUser) {
            $entitySql = "
            INSERT INTO {$this->_db->Entity} (
                email, 
                first_name, 
                last_name
            ) VALUES (?, ?, ?)";
            $this->_db->exec($entitySql, array($values['super_email'], 'Super', 'User'));

            $userSql = "
            INSERT INTO {$this->_db->User} (
                username, 
                password, 
                active, 
                role, 
                entity_id
            ) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
            $this->_db->exec($userSql, array($values['username'], $values['password']));
        }
        
        
        // Insert options.
        $optionSql = "
        INSERT INTO {$this->_db->Option} (
            name, 
            value
        ) VALUES (?, ?)";
        
        // Insert the form options to the options table.
        $options = array('administrator_email', 
                         'copyright', 
                         'site_title', 
                         'author', 
                         'description', 
                         'thumbnail_constraint', 
                         'square_thumbnail_constraint', 
                         'fullsize_constraint', 
                         'per_page_admin', 
                         'per_page_public', 
                         'show_empty_elements',
                         'path_to_convert');
        foreach ($options as $option) {
            $this->_db->exec($optionSql, array($option, $values[$option]));
        }
        
        // Insert default options to the options table. 
        $this->_db->exec($optionSql, array('migration', OMEKA_MIGRATION));
        $this->_db->exec($optionSql, array('admin_theme', 'default'));
        $this->_db->exec($optionSql, array('public_theme', 'default'));
        $this->_db->exec($optionSql, array('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('disable_default_file_validation', 0));
        
        return true;
    }

    public static function isInstalled(Omeka_Db $db)
    {        
        // Assume Omeka is not installed if the `options` table does not exist.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $db->fetchAll($sql);
        if (empty($tables)) {
            return false;
        }
        
        // Assume Omeka is not installed if the `options` table contains no rows.
        require_once 'Option.php';
        $optionCount = (int)$db->fetchOne("SELECT COUNT(id) FROM `{$db->prefix}options`");
        if (!$optionCount) {
            return false;
        }
        
        // Otherwise, assume Omeka is already installed.
        return true;
    }
    
    private function _getPathToConvert()
    {
        // Use the "which" command to auto-detect the path to ImageMagick;
        // redirect std error to where std input goes, which is nowhere. See: 
        // http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm. If $returnVar is "0" 
        // there was no error, so assign the output of the "which" command. See: 
        // http://us.php.net/manual/en/function.system.php#66795.
        $command = 'which convert 2>&0';
        $lastLineOutput = exec($command, $output, $returnVar);
        // Return only the directory component of the path returned.
        return $returnVar == 0 ? dirname($lastLineOutput) : '';
    }
}
