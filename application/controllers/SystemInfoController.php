<?php 

class SystemInfoController extends Omeka_Controller_Action
{
    private $_db;
    
	public function indexAction()
    {
        $this->_db = get_db();
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

        return apply_filters('system_info_array', $info);
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
        $themes = Theme::getAvailable();
        $currentTheme = get_option('public_theme');

        foreach ($themes as $name => $theme) {
            $themeInfo = $theme->version;

            if ($name == $currentTheme) {
                $themeInfo .= ' (current)';
            }
            
            $info['Themes'][$theme->title] = $themeInfo;
        }

        ksort($info['Themes']);
    }
}
