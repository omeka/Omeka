<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test the new migration manager.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Db_Migration_ManagerTest extends PHPUnit_Framework_TestCase
{
    const FUTURE_MIGRATION = '50000101120000';
    const ALREADY_RUN_MIGRATION = '20080717013526';
    const TARGET_MIGRATION = '20100401120000';
    
    public function setUp()
    {
        $this->db = $this->getMock('Omeka_Db', array('fetchCol', 'getAdapter'), array(), '', false);
        $this->dbAdapter = $this->getMock('Zend_Db_Adapter_Mysqli', array('insert'), array(), '', false);
        $this->db->expects($this->any())
                 ->method('getAdapter')
                 ->will($this->returnValue($this->dbAdapter));
        $migrationDir = dirname(__FILE__) . '/_files/migrations';
        $this->manager = new Omeka_Db_Migration_Manager($this->db, $migrationDir);
    }
    
    public function testMigratingToCurrentTime()
    {
        $this->db->expects($this->any())
                 ->method('fetchCol')
                 ->with($this->stringContains("SELECT"))
                 ->will($this->returnValue(array('20080717013528', self::ALREADY_RUN_MIGRATION)));
        $this->manager->migrate();
        // If all goes well, there will be no exceptions thrown.
        $this->assertTrue(Zend_Registry::isRegistered('ran_target_migration'));
    }
}
