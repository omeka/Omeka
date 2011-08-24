<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Installer task for inserting options into the options table.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_Options implements Installer_TaskInterface
{
    private $_expectedOptions = array(
        'administrator_email',
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
        'path_to_convert',
        Theme::ADMIN_THEME_OPTION,
        Theme::PUBLIC_THEME_OPTION,
        Omeka_Validate_File_Extension::WHITELIST_OPTION,
        Omeka_Validate_File_MimeType::WHITELIST_OPTION,
        File::DISABLE_DEFAULT_VALIDATION_OPTION,
        Omeka_Db_Migration_Manager::VERSION_OPTION_NAME,
        'display_system_info',
        'tag_delimiter',
    );
    
    private $_options = array();
        
    /**
     * Set the key value pairs that will correspond to database options.
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }
    
    public function install(Omeka_Db $db)
    {
        $givenOptions = array_keys($this->_options);
        if ($missingOptions = array_diff($this->_expectedOptions, $givenOptions)) {
            $optStr = join(', ', $missingOptions);
            throw new Installer_Task_Exception("Missing the following options: $optStr.");
        }
        if ($unknownOptions = array_diff($givenOptions, $this->_expectedOptions)) {
            $optStr = join(', ', $unknownOptions);
            throw new Installer_Task_Exception("Unknown options given: $optStr.");
        }
        
        foreach ($this->_options as $name => $value) {
            $db->insert('Option', array('name' => $name, 'value' => $value));
        }
    }
}
