<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The default installer, which extracts values from the installer form to 
 * create the default Omeka installation.
 * 
 * @package Omeka\Install
 */
class Installer_Default implements Installer_InstallerInterface
{
    const DEFAULT_USER_ACTIVE = true;
    const DEFAULT_USER_ROLE = 'super';
    
    const DEFAULT_PUBLIC_THEME = 'default';
    const DEFAULT_ADMIN_THEME = 'default';
    
    private $_db;
    private $_form;
    
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
     * Set the form from which to extract data for the installer.
     * 
     * @param Zend_Form $form
     */
    public function setForm(Zend_Form $form)
    {
        $this->_form = $form;
    }
    
    public function getDb()
    {
        return $this->_db;
    }

    public function install()
    {
        $this->getDb()->beginTransaction();

        $this->_createSchema();
        $this->_createUser();
        $this->_setupMigrations();
        $this->_addOptions();

        $this->getDb()->commit();
    }
    
    protected function _getValue($fieldName)
    {
        if (!$this->_form) {
            throw new Installer_Exception("Form was not set via setForm().");
        }
        
        return $this->_form->getValue($fieldName);
    }
    
    private function _createSchema()
    {
        $schemaTask = new Installer_Task_Schema();
        $schemaTask->useDefaultTables();
        $schemaTask->install($this->_db);
    }
    
    private function _createUser()
    {
        $userTask = new Installer_Task_User;
        $userTask->setUsername($this->_getValue('username'));
        $userTask->setPassword($this->_getValue('password'));
        $userTask->setEmail($this->_getValue('super_email'));
        $userTask->setName(Omeka_Form_Install::DEFAULT_USER_NAME);
        $userTask->setIsActive(Installer_Default::DEFAULT_USER_ACTIVE);
        $userTask->setRole(Installer_Default::DEFAULT_USER_ROLE);
        $userTask->install($this->_db);
    }

    private function _setupMigrations()
    {
        $task = new Installer_Task_Migrations();
        $task->install($this->_db);
    }
    
    private function _addOptions()
    {
        $task = new Installer_Task_Options();
        $task->setOptions(array(
            'administrator_email'           => $this->_getValue('administrator_email'), 
            'copyright'                     => $this->_getValue('copyright'), 
            'site_title'                    => $this->_getValue('site_title'), 
            'author'                        => $this->_getValue('author'), 
            'description'                   => $this->_getValue('description'), 
            'thumbnail_constraint'          => $this->_getValue('thumbnail_constraint'), 
            'square_thumbnail_constraint'   => $this->_getValue('square_thumbnail_constraint'), 
            'fullsize_constraint'           => $this->_getValue('fullsize_constraint'), 
            'per_page_admin'                => $this->_getValue('per_page_admin'), 
            'per_page_public'               => $this->_getValue('per_page_public'), 
            'show_empty_elements'           => $this->_getValue('show_empty_elements'),
            'path_to_convert'               => $this->_getValue('path_to_convert'),
            Theme::ADMIN_THEME_OPTION       => Installer_Default::DEFAULT_ADMIN_THEME,
            Theme::PUBLIC_THEME_OPTION      => Installer_Default::DEFAULT_PUBLIC_THEME,
            Omeka_Validate_File_Extension::WHITELIST_OPTION => Omeka_Validate_File_Extension::DEFAULT_WHITELIST,
            Omeka_Validate_File_MimeType::WHITELIST_OPTION  => Omeka_Validate_File_MimeType::DEFAULT_WHITELIST,
            File::DISABLE_DEFAULT_VALIDATION_OPTION         => (string)!extension_loaded('fileinfo'),
            Omeka_Db_Migration_Manager::VERSION_OPTION_NAME => OMEKA_VERSION,
            'display_system_info'           => true, 
            'html_purifier_is_enabled' => 1,
            'html_purifier_allowed_html_elements' => implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()),
            'html_purifier_allowed_html_attributes' => implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()),
            'tag_delimiter'                 => ',',
            Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME => Omeka_Navigation::getNavigationOptionValueForInstall(),
            'search_record_types' => serialize(get_search_record_types()), 
            'api_enable' => false, 
            'api_per_page' => 50,
            'show_element_set_headings' => 1
        ));
        $task->install($this->_db);
    }
    
    public function isInstalled()
    {
        // Assume Omeka is not installed if the `options` table does not exist.
        $sql = "SHOW TABLES LIKE '{$this->_db->prefix}options'";
        $tables = $this->_db->fetchAll($sql);
        return !empty($tables);
    }
}
