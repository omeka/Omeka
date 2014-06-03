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
    const DEFAULT_STRATEGY = 'Omeka_File_Derivative_Strategy_ExternalImageMagick';

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

        $strategyClass = empty($config->strategy) ? self::DEFAULT_STRATEGY : $config->strategy;
        $strategyOptions = empty($config->strategyOptions) ? array() : $config->strategyOptions->toArray();

        if ($strategyClass == self::DEFAULT_STRATEGY && empty($strategyOptions['path_to_convert'])) {
            if (!($convertPath = get_option('path_to_convert'))) {
                return null;
            }
            $strategyOptions['path_to_convert'] = $convertPath;
        }

        $strategy = new $strategyClass;
        if (!$strategy instanceof Omeka_File_Derivative_StrategyInterface) {
            throw new Omeka_File_Derivative_Exception('Invalid strategy configured.');
        }
        $strategy->setOptions($strategyOptions);

        $creator = new Omeka_File_Derivative_Creator;
        $creator->setStrategy($strategy);

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
