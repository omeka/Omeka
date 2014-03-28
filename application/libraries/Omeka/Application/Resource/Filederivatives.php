<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Bootstrap resource for configuring the derivative creator.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Filederivatives extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $bootstrap->bootstrap('Options');
        
        $config = $bootstrap->config->fileDerivatives;
        $options = $bootstrap->options;
        
        if ($config) {
            $derivativeOptions = $config->toArray();
        } else {
            $derivativeOptions = array();
        }

        if (!($convertPath = get_option('path_to_convert'))) {
            return null;
        }

        $creator = new Omeka_File_Derivative_Image_Creator($convertPath);

        if (isset($config->typeBlacklist)) {
            $creator->setTypeBlacklist($config->typeBlacklist->toArray());
        }

        if (isset($config->typeWhitelist)) {
            $creator->setTypeWhitelist($config->typeWhitelist->toArray());
        }

        Zend_Registry::set('file_derivative_creator', $creator);
        return $creator;
    }
}
