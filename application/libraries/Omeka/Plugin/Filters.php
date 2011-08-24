<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * This class augments the behavior of the Omeka_Plugin_Broker class.  The 
 * methods on it can be transparently accessed from the plugin broker class
 * via delegation.  This is more of an organizational thing because the plugin
 * broker class seems to be getting pretty cluttered.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Plugin_Filters
{
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
     * The plugin broker object.
     *
     * @var Omeka_Plugin_Broker
     */
    protected $_broker;
    
    /**
     * @param Omeka_Plugin_Broker $broker The plugin broker, to allow calls to
     * be delegated back to the broker.
     */
    public function __construct(Omeka_Plugin_Broker $broker)
    {
        $this->_broker = $broker;
    }
    
    /**
     * Delegate transparently to the Omeka_Plugin_Broker object.
     * 
     * @param string $m Method to call.
     * @param array $a Arguments to pass to method.
     * @return mixed
     */
    public function __call($m, $a)
    {
        return call_user_func_array(array($this->_broker, $m), $a);
    }
    
    /**
     * Add a filter implementation.
     *
     * @see Omeka_Plugin_Filters::applyFilters()
     * @param string|array $filterName Name of filter being implemented.
     * @param callback $callback PHP callback for filter implementation.
     * @param integer|null (optional) Priority. A lower priority will
     * cause a filter to be run before those with higher priority.
     * @return void
     */
    public function addFilter($filterName, $callback, $priority = 10)
    {               
        $this->_filters[$this->_getFilterKey($filterName)][$priority][$this->_getFilterNamespace()] = $callback;
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
     * @see Omeka_Plugin_Filters::addFilters()
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
     * @see Omeka_Plugin_Filters::addFilter()
     * @param mixed $filterName Name of the filter to apply.
     * @param mixed $value Value to be filtered.
     * @param array $otherParams Optional set of parameters to pass in addition
     * to the value to filter.  If these are passed, they will show up as 
     * sequential arguments to the filter implementation after the value to 
     * filter.
     * @return mixed Result of filtering $value.
     */
    public function applyFilters($filterName, $value, array $otherParams = array())
    {
        $filters = $this->getFilters($filterName);
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
                    $tempArgs = $otherParams;
                    array_unshift($tempArgs, $value);
                    $value = call_user_func_array($filter, $tempArgs);
                }
            }        
        }
        return $value;
    }
}
