<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Install
 */
class Installer_Task_Migrations implements Installer_TaskInterface
{    
    public function install(Omeka_Db $db)
    {
        $manager = Omeka_Db_Migration_Manager::getDefault($db);
        $manager->setupTimestampMigrations();
        $manager->markAllAsMigrated();
    }
}
