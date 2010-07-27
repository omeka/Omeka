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
class Installer_TestTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'omeka_';
    const USER_ID = 1;
    const ENTITY_ID = 2;
    const USERS_PLANS_ID = 3;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(),
            $this);
    }
    
    public function testRunInstaller()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::USERS_PLANS_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::USER_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::ENTITY_ID);
        // This should not throw any exceptions or anything like that.
        $installer = new Installer_Test($this->db);
        $installer->install();
        // Verify that it made SQL queries using the test data.
        $this->profilerHelper->assertDbQuery(array(
            "INSERT INTO `omeka_options`",
            array(1=>'thumbnail_constraint',
                  2=>Omeka_Form_Install::DEFAULT_THUMBNAIL_CONSTRAINT,
                  3=>'thumbnail_constraint',
                  4=>Omeka_Form_Install::DEFAULT_THUMBNAIL_CONSTRAINT)
        ), "Should have added options corresponding to test defaults.");
    }
}