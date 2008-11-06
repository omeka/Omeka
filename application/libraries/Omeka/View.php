<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @see Zend_View_Abstract
 */
require_once 'Zend/View/Abstract.php';

/**
 * Customized subclass of Zend Framework's View class.
 *
 * This adds the correct script paths for themes and plugins
 * so that controllers can render the appropriate scripts.
 *
 * This will also inject directly into the view scripts
 * all variables that have been assigned to the view,
 * so that theme writers can access them as $item instead of
 * $this->item, for example.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View extends Zend_View_Abstract
{    
    /**
     * Maintains a key => value pairing corresponding to hard path => web path for possible assets for Omeka views
     *
     * @var array
     **/
    protected $_asset_paths = array();
    
    public function __construct($config = array())
    {
        parent::__construct($config);         
        
        // Setting the XHTML1_STRICT doctype fixes validation errors for ZF's form elements
        $this->doctype()->setDoctype('XHTML1_STRICT');
        
        $this->addHelperPath(HELPER_DIR, 'Omeka_View_Helper');
    }
    
    /**
     * @return array
     **/
    public function getAssetPaths()
    {
        return $this->_asset_paths;
    }

    public function addAssetPath($physical, $web)
    {
        array_unshift($this->_asset_paths, array($physical, $web));
    }
    
    public function setAssetPath($physical, $web)
    {
        $this->_asset_paths = array();
        $this->_asset_paths[] = array($physical, $web);
    }
        
    /**
     * This allows for variables set to the view object
     * to be referenced in the view script by their actual name.
     * 
     * For example, in a controller you might do something like:
     * $view->assign('themes', $themes);
     * Normally in the view you would then reference $themes through:
     * $this->themes;
     * 
     * 
     * Now you can reference it simply by using:
     * $themes;
     * 
     * 
     */
    public function _run() {
        $vars = $this->getVars();
                
        require_once HELPERS;
        
        try {
            extract($vars);    
            include func_get_arg(0);
        } catch (Exception $e) {
            
            // Exceptions should not be uncaught at this stage of execution. 
            // This is b/c the only PHP executed beyond this point are theme 
            // functions.
            echo 'Error:' . $e->getMessage();
            
            if ($config = Omeka_Context::getInstance()->getConfig('basic')) {
                //Display a lot of info if exceptions are turned on
                if ($config->debug->exceptions) {    
                    echo nl2br( $e->getTraceAsString() );
                }                
            }
        }
    }
}