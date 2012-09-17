<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * A test migration that has not yet been run.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class notRunYet extends Omeka_Db_Migration_AbstractMigration
{
    
    public function up()
    {
        throw new Omeka_Db_Migration_Exception(__('This migration should not be run.'));
    }
}
