<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Installer/Requirements.php';

require_once dirname(__FILE__) . "/../forms/Install.php";

/*
-- Checkbox next to "Administrator Email": "Use Superuser Email"
-- Runtime JavaScript validation
*/
class Installer
{
    const SUPER_FIRST_NAME = 'Super';
    const SUPER_LAST_NAME = 'User';
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
            // Hack, prevents barfing when saving.
            Omeka_Context::getInstance()->setDb($db);
            
            $user = new User;
            $user->Entity = new Entity;
            $user->Entity->email = $values['super_email'];
            $user->Entity->first_name = self::SUPER_FIRST_NAME;
            $user->Entity->last_name = self::SUPER_LAST_NAME;
            $user->username = $values['username'];
            $user->setPassword($values['password']);
            $user->active = 1;
            $user->role = 'super';
            $user->forceSave();
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
        
        $this->_db->exec($optionSql, array('html_purifier_is_enabled', 1));
        $this->_db->exec($optionSql, array('html_purifier_allowed_html_elements', implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements())));
        $this->_db->exec($optionSql, array('html_purifier_allowed_html_attributes', implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes())));
        
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
    
    public function getPathToConvert()
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
