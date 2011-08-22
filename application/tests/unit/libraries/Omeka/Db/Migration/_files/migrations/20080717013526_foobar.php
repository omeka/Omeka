<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * A fake migration file to test Omeka_Db_Migration_Manager.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class foobar extends Omeka_Db_Migration
{
    public function up()
    {
        throw new Exception("This migration should have been run previously (should not run again).");
    }
}
