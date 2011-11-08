<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @package Omeka
 */

/**
 * Abstract plugin class.
 *
 * Plugin authors may inherit from this class to aid in building their plugin
 * framework.
 *
 * @package Omeka
 */
abstract class Omeka_Plugin_Abstract
{
    /**
     * Database object accessible to plugin authors.
     *
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * Plugin hooks.
     *
     * Plugin authors should give an array containing hook names as values.
     * Each hook should have a corresponding hookCamelCased() method defined 
     * in the child class. E.g: the after_save_form_record hook should 
     * have a corresponding hookAfterSaveFormRecord() method.
     *
     * @var array
     */
    protected $_hooks;
    
    /**
     * Plugin filters.
     *
     * Plugin authors should give an array containing filter names as values.
     * Each filter should have a corresponding filterCamelCased() method 
     * defined in the child class. E.g: the admin_navigation_main filter should 
     * have a corresponding filterAdminNavigationMain() method. 
     *
     * @var array
     */
    protected $_filters;
    
    /**
     * Plugin options.
     *
     * Plugin authors should give an array containing option names as keys and
     * their default values as values, if any. For example:
     * <code>
     * array('option_name1' => 'option_default_value1',
     *       'option_name2' => 'option_default_value2',
     *       'option_name3',
     *       'option_name4')
     * </code>
     *
     * @var array
     */
    protected $_options;
    
    /**
     * Construct the plugin object.
     *
     * Sets the database object. Plugin authors must call parent::__construct()
     * in the child class's constructor, if used.
     */
    public function __construct()
    {
        $this->_db = Omeka_Context::getInstance()->getDb();
    }
    
    /**
     * Set up the plugin to hook into Omeka.
     *
     * Adds the plugin's hooks and filters. Plugin writers must call this method
     * after instantiating their plugin class.
     */
    public function setUp()
    {
        $this->_addHooks();
        $this->_addFilters();
    }
    
    /**
     * Set options with default values.
     *
     * Plugin authors may want to use this convenience method in their install
     * hook callback.
     */
    protected function _installOptions()
    {
        $options = $this->_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            // Don't set options without default values.
            if (!is_string($name)) {
                continue;
            }
            set_option($name, $value);
        }
    }
    
    /**
     * Delete all options.
     *
     * Plugin authors may want to use this convenience method in their uninstall
     * hook callback.
     */
    protected function _uninstallOptions()
    {
        $options = self::$_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            delete_option($name);
        }
    }
    
    /**
     * Validate and add hooks.
     */
    private function _addHooks()
    {
        $hookNames = $this->_hooks;
        if (!is_array($hookNames)) {
            return;
        }
        foreach ($hookNames as $hookName) {
            $functionName = 'hook' . Inflector::camelize($hookName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Hook callback "' . $functionName . '" does not exist.');
            }
            add_plugin_hook($hookName, array($this, $functionName));
        }
    }
    
    /**
     * Validate and add filters.
     */
    private function _addFilters()
    {
        $filterNames = $this->_filters;
        if (!is_array($filterNames)) {
            return;
        }
        foreach ($filterNames as $filterName) {
            $functionName = 'filter' . Inflector::camelize($filterName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Filter callback "' . $functionName . '" does not exist.');
            }
            add_filter($filterName, array($this, $functionName));
        }
    }
}
