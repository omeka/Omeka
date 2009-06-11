<?php 


class Omeka_Core_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {        
        return new Zend_Config_Ini(CONFIG_DIR . DIRECTORY_SEPARATOR . 'config.ini', 'site');     
    }
}
