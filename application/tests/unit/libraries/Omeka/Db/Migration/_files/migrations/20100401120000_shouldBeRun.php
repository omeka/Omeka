<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * This migration should be run.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class shouldBeRun extends Omeka_Db_Migration
{
    
    public function up()
    {
        // This migration should run fine.
        Zend_Registry::set('ran_target_migration', true);
    }
}
