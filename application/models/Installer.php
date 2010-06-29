<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Creates and populates the Omeka database schema.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
 **/
class Installer
{
    const SUPER_FIRST_NAME = 'Super';
    const SUPER_LAST_NAME = 'User';
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    const DEFAULT_SHOW_EMPTY_ELEMENTS = '1';
    
    /**
     * @var Omeka_Db
     */
    private $_db;
    
    /**
     * Constructor.
     * 
     * @param Omeka_Db $db
     */
    public function __construct(Omeka_Db $db)
    {           
        $this->_db = $db;
    }
        
    /**
     * Install Omeka.
     * 
     * @param array $values Set of values required by the installer.  Usually
     * passed in via the form.
     * @param boolean $createUser Whether or not to create a new user along with
     * this installation.  Defaults to true.
     */
    public function install(array $values, $createUser = true)
    {
        if ($this->isInstalled()) {
            throw new RuntimeException("Omeka has already been installed.");
        }
        
        $db = $this->_db;
        
        $this->_createSchema();
        
        if ($createUser) {            
            $this->_createDefaultUser($values['username'], 
                                      $values['password'],
                                      $values['super_email']);
        }
        
        $this->_createMigrationTable();
        
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
        $this->_db->exec($optionSql, array('admin_theme', 'default'));
        $this->_db->exec($optionSql, array('public_theme', 'default'));
        $this->_db->exec($optionSql, array('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('disable_default_file_validation', 0));
                
        // If the fileinfo extension is not installed.
        $this->_db->exec($optionSql, array('enable_header_check_for_file_mime_types', (string)!extension_loaded('fileinfo')));
                
        return true;
    }
    
    /**
     * Determine whether Omeka has been installed.
     */
    public function isInstalled()
    {        
        $db = $this->_db;
        
        // Assume Omeka is not installed if the `options` table does not exist.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $db->fetchAll($sql);
        if (empty($tables)) {
            return false;
        }
        
        // Assume Omeka is not installed if the `options` table contains no rows.
        $optionCount = (int)$db->fetchOne("SELECT COUNT(id) FROM `{$db->prefix}options`");
        if (!$optionCount) {
            return false;
        }
        
        // Otherwise, assume Omeka is already installed.
        return true;
    }
    
    private function _createSchema()
    {
        $db = $this->_db;
        include INSTALL_DIR . DIRECTORY_SEPARATOR . 'install.sql.php';
        $this->_db->execBlock($installSql);
    }
    
    private function _createDefaultUser($username, $password, $email)
    {
        // Hack, prevents barfing when saving.
        Omeka_Context::getInstance()->setDb($this->_db);
        $user = new User;
        $user->Entity = new Entity;
        $user->Entity->email = $email;
        $user->Entity->first_name = self::SUPER_FIRST_NAME;
        $user->Entity->last_name = self::SUPER_LAST_NAME;
        $user->username = $username;
        $user->setPassword($password);
        $user->active = 1;
        $user->role = 'super';
        $user->forceSave();
    }
    
    private function _createMigrationTable()
    {
        $migrations = Omeka_Db_Migration_Manager::getDefault($this->_db);
        $migrations->setupTimestampMigrations();
        $migrations->markAllAsMigrated();
    }
    
    private function _setupOptions()
    {
        
    }
}
