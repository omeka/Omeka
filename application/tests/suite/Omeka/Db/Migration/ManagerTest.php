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
class Omeka_Db_Migration_ManagerTest extends Omeka_Test_TestCase
{
    const FUTURE_MIGRATION = '50000101120000';
    const ALREADY_RUN_MIGRATION = '20080717013526';
    const TARGET_MIGRATION = '20100401120000';

    public function setUpLegacy()
    {
        $this->db = $this->getMockBuilder('Omeka_Db')
            ->setMethods(array('fetchCol', 'getAdapter', 'query'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->dbAdapter = $this->getMock('Zend_Db_Adapter_Mysqli', array('insert'), array(), '', false);
        $this->db->expects($this->any())
                 ->method('getAdapter')
                 ->will($this->returnValue($this->dbAdapter));
        $migrationDir = dirname(__FILE__) . '/_files/migrations';
        $this->manager = new Omeka_Db_Migration_Manager($this->db, $migrationDir);
        $this->bootstrap = new Omeka_Test_Bootstrap;
        $this->bootstrap->getContainer()->options = array(Omeka_Db_Migration_Manager::VERSION_OPTION_NAME => '2.0');
        Zend_Registry::set('bootstrap', $this->bootstrap);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
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

    public function testMigratingWithPre20Version()
    {
        $this->db->expects($this->any())
                ->method('fetchCol')
                ->with($this->stringContains('SELECT'))
                ->will($this->returnValue(array(self::ALREADY_RUN_MIGRATION)));
        $this->db->expects($this->once())
                ->method('query')
                ->with("SET SESSION sql_mode=''");

        $this->bootstrap->getContainer()->options[Omeka_Db_Migration_Manager::VERSION_OPTION_NAME] = '1.0';
        $this->manager->migrate();
    }
}
