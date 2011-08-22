<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
