<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract plugin class.
 *
 * Plugin authors may inherit from this class to aid in building their plugin
 * framework.
 * 
 * @package Omeka\Plugin
 */
abstract class Omeka_Plugin_AbstractPlugin
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
     * In the child class plugin authors should set an array containing hook 
     * names as values and, optionally, callback names as keys. If a callback 
     * name is given, the child class should contain an identically named 
     * method. If no callback key is given, the child class should contain a 
     * corresponding hookCamelCased() method. E.g: the after_save_form_record 
     * filter should have a corresponding hookAfterSaveRecord() method.
     * 
     * For example:
     * <code>
     * array('install', 
     *       'uninstall', 
     *       'doSomething' => 'after_save_item')
     * </code>
     *
     * @var array
     */
    protected $_hooks;
    
    /**
     * Plugin filters.
     *
     * In the child class plugin authors should set an array containing filter 
     * names as values and, optionally, callback names as keys. If a callback 
     * name is given, the child class should contain an identically named 
     * method. If no callback key is given, the child class should contain a 
     * corresponding filterCamelCased() method. E.g: the admin_navigation_main 
     * filter should have a corresponding filterAdminNavigationMain() method.
     * 
     * For example:
     * <code>
     * array('admin_navigation_main', 
     *       'public_navigation_main', 
     *       'changeSomething' => 'display_option_site_title', 
     *       'displayItemDublinCoreTitle' => array('Display', 'Item', 'Dublin Core', 'Title'))
     * </code>
     *
     * @var array
     */
    protected $_filters;
    
    /**
     * Plugin options.
     *
     * Plugin authors should give an array containing option names as keys and
     * their default values as values, if any.
     * 
     * For example:
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
        $this->_db = Zend_Registry::get('bootstrap')->getResource('Db');
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
        if (!is_array($this->_options)) {
            return;
        }
        foreach ($this->_options as $name => $value) {
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
        if (!is_array($this->_options)) {
            return;
        }
        foreach ($this->_options as $name => $value) {
            if (!is_string($name)) {
                $name = $value;
            }
            delete_option($name);
        }
    }
    
    /**
     * Validate and add hooks.
     */
    private function _addHooks()
    {
        if (!is_array($this->_hooks)) {
            return;
        }
        foreach ($this->_hooks as $callback => $hook) {
            if (!is_string($callback)) {
                $callback = 'hook' . Inflector::camelize($hook);
            }
            if (!is_callable(array($this, $callback))) {
                throw new Omeka_Plugin_Exception('Hook callback "' . $callback . '" does not exist.');
            }
            add_plugin_hook($hook, array($this, $callback));
        }
    }
    
    /**
     * Validate and add filters.
     */
    private function _addFilters()
    {
        if (!is_array($this->_filters)) {
            return;
        }
        foreach ($this->_filters as $callback => $filter) {
            if (!is_string($callback)) {
                $callback = 'filter' . Inflector::camelize($filter);
            }
            if (!is_callable(array($this, $callback))) {
                throw new Omeka_Plugin_Exception('Filter callback "' . $callback . '" does not exist.');
            }
            add_filter($filter, array($this, $callback));
        }
    }
}
