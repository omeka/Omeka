<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Plugin Broker for Omeka.
 *
 * For example, $broker->callHook('add_action_contexts', $controller)
 * would call the 'add_action_contexts' on all plugins, and it would
 * provide the controller object as the first argument to all
 * implementations of that hook.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Plugin_Broker
{
    /**
     * Array of hooks that have been implemented for plugins.
     *
     * @var array
     */
    protected $_callbacks = array();

    /**
     * Stores all defined filters.
     *
     * Storage in array where $_filters['filterName']['priority']['plugin'] = $hook;
     *
     * @todo Should this storage method be merged into the Plugin Broker class?
     * Probably.  That way hooks and filters will be no different in the storage
     * space (in the manner of Wordpress).
     * @var array
     */
    protected $_filters = array();

    /**
     * The directory name of the current plugin (used for calling hooks)
     *
     * @var string
     */
    protected $_current;

    /**
     * Add a hook implementation for a plugin.
     *
     * @param string $hook Name of the hook being implemented.
     * @param string $callback PHP callback for the hook implementation.
     * @param string|null $plugin Optional name of the plugin for
     * which to add the hook. If omitted, the current plugin is used.
     * @return void
     */
    public function addHook($hook, $callback, $plugin = null)
    {
        if ($plugin) {
            $currentPluginDirName = $plugin;
        } else {
            $currentPluginDirName = $this->getCurrentPluginDirName();
        }

        // Null or empty plugin name leads to false negatives when
        // looking up callbacks.
        if (!$currentPluginDirName) {
            throw new RuntimeException("Cannot add a hook without an "
                . "associated plugin namespace.");
        }

        $this->_callbacks[$hook][$currentPluginDirName] = $callback;
    }

    /**
     * Get the hook implementation for a plugin.
     *
     * @param string $pluginDirName Name of the plugin to get the implementation
     * from.
     * @param string $hook Name of the hook to get the implementation for.
     * @return callback|null
     */
    public function getHook($pluginDirName, $hook)
    {
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }

        if (array_key_exists($hook, $this->_callbacks)
            && is_array($this->_callbacks[$hook])
            && array_key_exists($pluginDirName, $this->_callbacks[$hook])
        ) {
            return $this->_callbacks[$hook][$pluginDirName];
        }
        return null;
    }

    /**
     * Set the currently-focused plugin by directory name.
     *
     * The plugin helper functions do not have any way of determining what
     * plugin to is currently in focus.  These get/setCurrentPluginDirName
     * methods allow the broker to know how to delegate to specific plugins if
     * necessary.
     *
     * @param string $pluginDirName Plugin to set as current.
     * @return void
     */
    public function setCurrentPluginDirName($pluginDirName)
    {
        $this->_current = $pluginDirName;
    }

    /**
     * Get the directory name of the currently-focused plugin.
     *
     * @see Omeka_Plugin_Broker::setCurrentPluginDirName()
     * @return string
     */
    public function getCurrentPluginDirName()
    {
        return $this->_current;
    }

    /**
     * Call a hook by name.
     * Hooks can either be called globally or for a specific plugin only.
     *
     * @param string $hook Name of the hook.
     * @param array $args Arguments that are passed to each hook implementation.
     * @param Plugin|string $plugin Optional name of the plugin for which to
     * invoke the hook.
     * @return void
     */
    public function callHook($hook, $args = array(), $plugin = null)
    {
        if (empty($this->_callbacks[$hook])) {
            return;
        }

        // If we are calling the hook for a single function, do that and return.
        if ($plugin) {
            if ($callback = $this->getHook($plugin, $hook)) {
                call_user_func_array($callback, $args);
            }
            return;
        }

        // Otherwise iterate through all the hooks and call each in turn.
        foreach ($this->_callbacks[$hook] as $pluginDirName => $callback) {
            //Make sure the callback executes within the scope of the current plugin
            $this->setCurrentPluginDirName($pluginDirName);
            call_user_func_array($callback, $args);
        }

        // Reset the value for current plugin after this loop finishes
        $this->setCurrentPluginDirName(null);
    }

    /**
     * Add a filter implementation.
     *
     * @see applyFilters()
     * @param string|array $name Name of filter being implemented.
     * @param callback $callback PHP callback for filter implementation.
     * @param integer|null (optional) Priority. A lower priority will
     * cause a filter to be run before those with higher priority.
     * @return void
     */
    public function addFilter($name, $callback, $priority = 10)
    {
        $this->_filters[$this->_getFilterKey($name)][$priority][$this->_getFilterNamespace()] = $callback;
    }

    /**
     * Retrieve the namespace to use for the filter to be added.
     *
     * @return string Name of the current plugin (if applicable). Otherwise, a
     * magic constant that denotes globally applied filters.
     */
    protected function _getFilterNamespace()
    {
        if($pluginName = $this->getCurrentPluginDirName()) {
            return $pluginName;
        }

        return '__global__';
    }

    /**
     * Retrieve the key used for indexing the filter. The filter name should be
     * either a string or an array of strings. If the filter name is an object,
     * that might cause fiery death when using the serialized value for an array
     * key.
     *
     * @see addFilters()
     * @param string|array $name Filter name.
     * @return string Key for filter indexing.
     */
    protected function _getFilterKey($name)
    {
        return is_string($name) ? $name : serialize($name);
    }

    /**
     * Return all the filters for a specific hook in the correct order of
     * execution.
     *
     * @param string|array $hookName Filter name.
     * @return array Indexed array of filter callbacks.
     */
    public function getFilters($hookName)
    {
        $filterKey = $this->_getFilterKey($hookName);
        if (!isset($this->_filters[$filterKey])) {
            return array();
        }

        $filters = (array) $this->_filters[$filterKey];

        ksort($filters);

        return $filters;
    }

    /**
     * Clear all implementations for a filter (or all filters).
     *
     * @param string|null $name The name of the filter to clear.  If
     *  null or omitted, all filters will be cleared.
     * @return void
     */
    public function clearFilters($name = null)
    {
        if ($name) {
            unset($this->_filters[$this->_getFilterKey($name)]);
        } else {
            $this->_filters = array();
        }
    }

    /**
     * Run an arbitrary value through a set of filters.
     *
     * @see addFilter()
     * @param mixed $name The filter name.
     * @param mixed $value The value to filter.
     * @param array $options Additional options to pass to filter implementations.
     * @return mixed Result of applying filters to $value.
     */
    public function applyFilters($name, $value, array $options = array())
    {
        $filters = $this->getFilters($name);
        if ($filters) {
            // Filters are indexed by priority, then by plugin name.
            foreach ($filters as $priority => $filterSet) {
                // Each set of filters has a key that corresponds to a plugin
                // name, but that's not particularly important for this
                // particular loop. It only matters during lookup to determine
                // whether or not a specific filter has been set already.
                foreach ($filterSet as $filter) {
                    // The value must be prepended to the argument set b/c it is
                    // always the first argument to any filter callback.
                    if ($options) {
                        $value = call_user_func($filter, $value, $options);
                    } else {
                        $value = call_user_func($filter, $value);
                    }
                }
            }
        }
        return $value;
    }

    /**
     * Register the plugin broker so that plugin writers can use global functions
     * like add_plugin_hook() to interact with the plugin API.
     *
     * @return void
     */
    public function register()
    {
        Zend_Registry::set('pluginbroker', $this);
    }
}
