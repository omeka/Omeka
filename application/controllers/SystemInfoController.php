<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class SystemInfoController extends Omeka_Controller_AbstractActionController
{
    public function preDispatch()
    {
        if (!get_option('display_system_info')) {
            // Requires forward in addition to redirect because of ZF bug:
            // http://framework.zend.com/issues/browse/ZF-7496
            $request = $this->getRequest();
            $request->setActionName('index');
            $request->setControllerName('index');
            $request->setDispatched(false);
            return $this->_helper->redirector->gotoUrl('/');
        }
    }

    public function indexAction()
    {
        $this->_db = $this->_helper->db->getDb();
        $this->view->info = $this->_getInfoArray();
    }

    private function _getInfoArray()
    {
        $info['User']['Browser'] = $_SERVER['HTTP_USER_AGENT'];
        $info['User']['Role'] = current_user()->role;

        $info['System']['Omeka'] = OMEKA_VERSION;
        $info['System']['PHP'] = phpversion() . ' (' . php_sapi_name() . ')';
        $uname = php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m');
        $info['System']['OS'] = $uname;
        $info['System']['MySQL Server'] = $this->_db->getServerVersion();
        $info['System']['MySQL Client'] = mysqli_get_client_info();

        if (function_exists('apache_get_version')) {
            $apacheVersion = apache_get_version();
            if ($apacheVersion) {
                $info['System']['Apache'] = $apacheVersion;
            }
        }

        $this->_addExtensionInfo($info);
        $this->_addPluginInfo($info);
        $this->_addThemeInfo($info);

        return apply_filters('system_info', $info);
    }

    private function _addExtensionInfo(&$info)
    {
        $phpExtensions = get_loaded_extensions();
        $zendExtensions = get_loaded_extensions(true);

        natcasesort($phpExtensions);
        $info['PHP Extensions']['Regular'] = implode(', ', $phpExtensions);

        if (!empty($zendExtensions)) {
            natcasesort($zendExtensions);
            $info['PHP Extensions']['Zend'] = implode(', ', $zendExtensions);
        }
    }

    private function _addPluginInfo(&$info)
    {
        $pluginTable = $this->_db->getTable('Plugin');
        $plugins = $pluginTable->findAll();
        $info['Plugins'] = array();

        foreach ($plugins as $plugin) {
            $inactive = $plugin->active == '0';
            $pluginInfo = $plugin->version;
            $name = $plugin->name;
            if ($inactive) {
                $pluginInfo .= ' (inactive)';
            }
            $info['Plugins'][$name] = $pluginInfo;
        }

        ksort($info['Plugins']);
    }

    private function _addThemeInfo(&$info)
    {
        $themes = Theme::getAllThemes();
        $currentTheme = get_option('public_theme');
        $info['Themes'] = array();

        foreach ($themes as $name => $theme) {
            $themeInfo = @$theme->version;

            if ($name == $currentTheme) {
                $themeInfo .= ' (current)';
            }

            $info['Themes'][$theme->title] = $themeInfo;
        }

        ksort($info['Themes']);
    }
}
