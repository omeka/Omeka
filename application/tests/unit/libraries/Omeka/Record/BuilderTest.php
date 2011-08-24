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
class Omeka_Record_BuilderTest extends PHPUnit_Framework_TestCase
{   
    const DUMMY_RECORD_ID = 1;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter);
    }

    public function testConstructor()
    {
        $builder = new DummyRecordBuilder($this->db);
        $this->assertThat($builder, $this->isInstanceOf('Omeka_Record_Builder'));
    }
        
    public function testSetRecordMetadata()
    {
        $builder = new DummyRecordBuilder($this->db);
        $builder->setRecordMetadata(array(
            'description' => 'foobar'
        ));
        $this->assertEquals(array('description'=>'foobar'), $builder->getRecordMetadata());
    }
    
    public function testBuildIgnoresUnsettableRecordColumns()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $builder = new DummyRecordBuilder($this->db);
        $builder->setTest($this);
        $builder->setRecordMetadata(array(
            'id' => 3,
            'shazbot' => true
        ));
        $record = $builder->build();
        $this->assertFalse(isset($record->shazbot));
        $this->assertNotEquals(3, $record->id);
    }
    
    public function testGetRecordReturnsUnsavedRecord()
    {
        $builder = new DummyRecordBuilder($this->db);
        $record = $builder->getRecord();
        $this->assertThat($record, $this->isInstanceOf('DummyRecordBuilderRecord'));
        $this->assertFalse($record->exists());
    }

    /**
     * @expectedException Omeka_Record_Builder_Exception
     */
    public function testSetRecordRequiresCorrectRecordClass()
    {
        $builder = new DummyRecordBuilder($this->db);
        $builder->setRecord(new Item($this->db));
    }
    
    public function testSetRecordUsingRecordInstance()
    {
        $builder = new DummyRecordBuilder($this->db);
        $record = new DummyRecordBuilderRecord($this->db);
        $builder->setRecord($record);
        $this->assertSame($builder->getRecord(), $record);
    }
    
    public function testSetRecordUsingIntegerId()
    {
        $builder = new DummyRecordBuilder($this->db);
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(
            array(
                array('id' => self::DUMMY_RECORD_ID, 'description' => 'foobar')
            )
        ));
        $builder->setRecord(self::DUMMY_RECORD_ID);
        $record = $builder->getRecord();
        $this->assertThat($record, $this->isInstanceOf('DummyRecordBuilderRecord'));
        $this->assertTrue($record->exists());        
    } 
                    
    public function testBeforeBuildHook()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $this->assertFalse(isset($this->ranBeforeBuild));
        $builder = new DummyRecordBuilder($this->db);
        $builder->setTest($this);
        $builder->build();
        $this->assertTrue(isset($this->ranBeforeBuild));        
    }
    
    public function testAfterBuildHook()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $this->assertFalse(isset($this->ranAfterBuild));
        $builder = new DummyRecordBuilder($this->db);
        $builder->setTest($this);
        $builder->build();
        $this->assertTrue(isset($this->ranAfterBuild),
            "build() should have called the _afterBuild() method on the builder class.");        
    }
    
    public function testBuildReturnsSavedRecord()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::DUMMY_RECORD_ID);
        $builder = new DummyRecordBuilder($this->db);
        $builder->setTest($this);
        $record = $builder->build();       
        $this->assertTrue($record->exists(),
            "Returned record should have been saved.");         
    }    
}

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class DummyRecordBuilder extends Omeka_Record_Builder
{
    protected $_recordClass = 'DummyRecordBuilderRecord';
    protected $_settableProperties = array('description');
    
    private $_test;
    
    public function setTest(PHPUnit_Framework_TestCase $test)
    {
        $this->_test = $test;
    }
    
    protected function _beforeBuild(Omeka_Record $record)
    {
        $this->_test->ranBeforeBuild = true;
        $this->_test->assertThat($record, $this->_test->isInstanceOf('DummyRecordBuilderRecord'));
    }
    
    protected function _afterBuild(Omeka_Record $record)
    {
        $this->_test->assertTrue($record->exists());
        $this->_test->ranAfterBuild = true;
    }
}

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class DummyRecordBuilderRecord extends Omeka_Record
{
    public $description;
}
