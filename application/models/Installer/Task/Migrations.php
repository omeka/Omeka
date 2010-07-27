<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
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
