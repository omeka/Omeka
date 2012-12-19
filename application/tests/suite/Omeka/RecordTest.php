<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_RecordTest extends PHPUnit_Framework_TestCase
{   
    const VALIDATION_ERROR = "Do Not Set: Do Not Set property will automatically invalidate the record.";
     
    const DUMMY_RECORD_ID = 1; 
     
    private static $_eventStack = array();

    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->pluginBroker = new Omeka_Plugin_Broker;
    }
    
    /**
     * @expectedException Omeka_Record_Exception
     */
    public function testConstructorThrowsExceptionIfNoDatabasePresent()
    {
        try {
            $record = new DummyRecord;
        } catch (Exception $e) {
            $this->assertContains("Unable to retrieve database instance", $e->getMessage());
            throw $e;
        }
    }
    
    public function testGetRelatedMetadata()
    {
        $record = new DummyRecord($this->db);
        $this->assertNull($record->getFoobar());
        $record->setFoobar(true);
        $this->assertTrue($record->Foobar);
    }
    
    public function testGetSetPluginBroker()
    {
        $record = new DummyRecord($this->db);
        $this->assertNull($record->getPluginBroker());

        $bootstrap = new Omeka_Test_Bootstrap;
        $bootstrap->getContainer()->pluginbroker = $this->pluginBroker;
        Zend_Registry::set('bootstrap', $bootstrap);
        
        $this->assertSame($this->pluginBroker, $record->getPluginBroker());
        $mockPluginBroker = $this->getMock('Omeka_Plugin_Broker', array(), array(), '', false);
        $record->setPluginBroker($mockPluginBroker);
        $this->assertSame($mockPluginBroker, $record->getPluginBroker());
    }
    
    public function testDelegateToMixinMethod()
    {
        $record = new DummyRecord($this->db);
        $this->assertTrue($record->dummyMixinMethod());
    }
    
    public function testAddHasGetErrors()
    {
        $record = new DummyRecord($this->db);
        $this->assertFalse($record->hasErrors());
        $this->assertEquals("", (string)$record->getErrors());
        $record->addError('Whatever', "This error message can be whatever we want.");
        $this->assertTrue($record->hasErrors());
        $this->assertEquals("Whatever: This error message can be whatever we want.",
            (string)$record->getErrors());
    }
    
    public function testAddErrorsFromOtherRecord()
    {
        $record = new DummyRecord($this->db);
        $otherRecord = new DummyRecord($this->db);
        $otherRecord->addError("Random", "This error was from a different record.");
        $record->addErrorsFrom($otherRecord);
        $this->assertTrue($record->hasErrors());
        $this->assertEquals("Random: This error was from a different record.",
            (string)$record->getErrors());
    }
        
    public function testValidation()
    {
        // Dummy record should be valid by default
        $record = new DummyRecord($this->db);
        $this->assertTrue($record->isValid());
        
        $record->do_not_set = true;
        $this->assertFalse($record->isValid());
        
        $this->assertContains(self::VALIDATION_ERROR,
                              (string)$record->getErrors());
    }
    
    public function testExists()
    {
        $record = new DummyRecord($this->db);
        $this->assertFalse($record->exists());
        $record->id = 1;
        $this->assertTrue($record->exists());
        
        // Should fail with non-numeric ids as well.
        $record->id = '; DELETE FROM items; --';
        $this->assertFalse($record->exists());
    }
    
    /**
     * @expectedException Omeka_Record_Exception
     */
    public function testSaveLockedRecord()
    {
        $record = new DummyRecord($this->db);
        $record->lock();
        try {
            $record->save();
        } catch (Exception $e) {
            $this->assertContains("Cannot save a locked record", $e->getMessage());
            throw $e;
        }
    }

    /**
     * @expectedException Omeka_Record_Exception
     */
    public function testDeleteLockedRecord()
    {
        $record = new DummyRecord($this->db);
        $record->lock();
        try {
            $record->delete();
        } catch (Exception $e) {
            $this->assertContains("Cannot delete a locked record", $e->getMessage());
            throw $e;
        }
    }
        
    public function testGetTable()
    {
        $record = new DummyRecord($this->db);
        $dummyTable = new Omeka_Db_Table('DummyRecord', $this->db);
        $this->db->setTable('DummyRecord', $dummyTable);
        $this->assertSame($dummyTable, $record->getTable());
        
        $otherTable = new Omeka_Db_Table('Other', $this->db);
        $this->db->setTable('Other', $otherTable);
        $this->assertSame($otherTable, $record->getTable('Other'));
    }    
    
    public function testGetDb()
    {
        $record = new DummyRecord($this->db);
        $this->assertSame($this->db, $record->getDb());
    }
    
    public function testToArray()
    {
        $record = new DummyRecord($this->db);
        $record->id = 1;
        $record->do_not_set = 'foobar';
        $dummyTable = new Omeka_Db_Table('DummyRecord', $this->db);
        $this->db->setTable('DummyRecord', $dummyTable);
        $this->assertEquals(array('id' => 1, 'do_not_set' => 'foobar', 'other_field' => null), $record->toArray());
    }    
        
    public function testSaveInsertsNewRecord()
    {   
        $this->dbAdapter->appendLastInsertIdToStack(5);
        $record = new DummyRecord($this->db);
        $record->save();
        $queryProfile = $this->dbAdapter->getProfiler()->getLastQueryProfile();
        $this->assertNotNull($queryProfile);
        $this->assertContains("INSERT INTO `dummy_records`", $queryProfile->getQuery());
        $this->assertTrue($record->exists());
        $this->assertEquals(5, $record->id);
    }
    
    public function testSaveUpdatesExistingRecord()
    {
        $record = new DummyRecord($this->db);
        $record->id = 4;
        $record->save();
        $this->assertContains('DummyRecord::beforeSave(), insert = false', self::$_eventStack);
    }
    
    public function testSaveFiresCallbacksInCorrectOrder()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $record = new DummyRecord($this->db);
        $record->save();
        $this->assertEquals(array(
            'DummyRecord::beforeSave(), insert = true',
            'DummyRecord::afterSave(), insert = true',
        ), $this->_simpleStack());
    }
    
    /**
     * @expectedException Omeka_Validate_Exception
     */
    public function testForceSaveThrowsExceptionForUnsaveableRecord()
    {
        $record = new DummyRecord($this->db);
        $record->do_not_set = true;
        try {
            $record->save();
        } catch (Exception $e) {
            $this->assertContains(self::VALIDATION_ERROR, $e->getMessage());
            throw $e;
        }        
    }
    
    public function testClone()
    {
        $record = new DummyRecord($this->db);
        $record->id = 4;
        $this->assertTrue($record->exists());
        $clonedRecord = clone $record;
        $this->assertFalse($clonedRecord->exists());
    }
    
    public function testDelete()
    {
        $record = new DummyRecord($this->db);
        $record->id = 2;
        $record->delete();
        $queryProfile = $this->dbAdapter->getProfiler()->getLastQueryProfile();
        $this->assertThat($queryProfile, $this->isInstanceOf('Zend_Db_Profiler_Query'));
        $this->assertContains("DELETE FROM dummy_records WHERE (id = 2)", $queryProfile->getQuery());
        $this->assertFalse($record->exists());
        $this->assertNull($record->id);
    }
    
    public function testDeleteWithInvalidId()
    {
        $record = new DummyRecord($this->db);
        $record->id = '; DELETE FROM items; --';
        $this->assertFalse($record->delete());
    }
    
    public function testSetArray()
    {
        $record = new DummyRecord($this->db);
        $record->setArray(array('id' => 1, 'do_not_set' => 'whatever'));
        $this->assertEquals(1, $record->id);
        $this->assertEquals('whatever', $record->do_not_set);
    }
        
    /**
     * @expectedException Omeka_Validate_Exception
     */
    public function testSaveFormThrowsExceptionForInvalidPost()
    {
        $record = new DummyRecord($this->db);
        $post = array(
            'do_not_set' => 'foobar'
        );
        try {
            $record->setPostData($post);
            $record->save();
        } catch (Exception $e) {
            $this->assertContains(self::VALIDATION_ERROR, $e->getMessage());
            throw $e;
        }
    }
    
    public function testSetFromPostBlocksIdField()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $record = new DummyRecord($this->db);
        $post = array(
            'id' => 2,
        );    
        $record->setPostData($post);
        $this->assertNotEquals(2, $record->id);
    }
        
    public function tearDown()
    {
        self::$_eventStack = array();
        Zend_Registry::_unsetInstance();
    }
    
    public static function addToEventStack($event)
    {
        self::$_eventStack[] = $event;
    }
    
    private function _simpleStack()
    {
        $stack = self::$_eventStack;
        // Clear out the mixin/plugin hook events so we can see the basic 
        // callback structure.
        foreach ($stack as $key => $event) {
            if (strpos($event, "DummyMixin") !== false) {
                unset($stack[$key]);
            }
        }
        $stack = array_values($stack);
        return $stack;
    }
}

class DummyRecord extends Omeka_Record_AbstractRecord
{   
    /**
     * Setting this property will automatically invalidate the record.
     */
    public $do_not_set = null;
    
    public $other_field = null;
    
    protected $_related = array('Foobar' => 'getFoobar');
    
    protected function _initializeMixins()
    {
        $this->_mixins[] = new DummyMixin($this);
    }
    
    public function getFoobar()
    {
        return $this->_foobar;
    }
    
    public function setFoobar($flag)
    {
        $this->_foobar = $flag;
    }
    
    protected function beforeSave($args)
    {
        if ($args['insert']) {
            Omeka_RecordTest::addToEventStack('DummyRecord::beforeSave(), insert = true');
        } else {
            Omeka_RecordTest::addToEventStack('DummyRecord::beforeSave(), insert = false');
        }
    }
    
    protected function afterSave($args)
    {
        if ($args['insert']) {
            Omeka_RecordTest::addToEventStack('DummyRecord::afterSave(), insert = true');
        } else {
            Omeka_RecordTest::addToEventStack('DummyRecord::aftrerSave(), insert = false');
        }
    }
    
    /**
     * Executes before the record is deleted.
     */
    protected function beforeDelete() {
        Omeka_RecordTest::addToEventStack('DummyRecord::beforeDelete()');
    }
    
    /**
     * Executes after the record is deleted.
     */
    protected function afterDelete() {
        Omeka_RecordTest::addToEventStack('DummyRecord::afterDelete()');
    }
    
    protected function _validate()
    {
        if ($this->do_not_set) {
            $this->addError('do_not_set', "Do Not Set property will automatically invalidate the record.");
        }
    }
}

class DummyMixin extends Omeka_Record_Mixin_AbstractMixin
{
    public function dummyMixinMethod()
    {
        return true;
    }
    
    public function beforeSave($args)
    {
        Omeka_RecordTest::addToEventStack('DummyMixin::beforeSave()');
    }
}
