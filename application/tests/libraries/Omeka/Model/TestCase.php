<?php
/**
 * Encapsulates loading and configuring access to the test database.
 *
 * @package Omeka_Testing
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_Model_TestCase extends Omeka_Controller_TestCase
{            
    public function getAdapter()
    {
        return $this->core->getResource('Db');
    }
    
    protected function _setUpBootstrap($bootstrap)
    {
        $bootstrap->registerPluginResource('Db');
    }
} 