<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Models_CollectionTest extends Omeka_Test_AppTestCase
{
    //const COLLECTION_ID = 1;
    const USER_ID = 5;

    private $collection;

    public function setUpLegacy()
    {
        parent::setUpLegacy();
        $this->collection = new Collection($this->db);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testTotalItemsGetsCountFromItemsTable()
    {
        $collectionId = 1;

        $dbAdapter = new Zend_Test_DbAdapter;
        $dbAdapter->appendLastInsertIdToStack($collectionId);
        $db = new Omeka_Db($dbAdapter);
        $this->collection = new Collection($db);
        $profilerHelper = new Omeka_Test_Helper_DbProfiler($db->getAdapter()->getProfiler(), $this);
        $this->collection->totalItems();

        $profilerHelper->assertDbQuery("SELECT COUNT(DISTINCT(items.id)) FROM items");
    }

    public function testTotalItems()
    {
        $collectionId = 1;

        $dbAdapter = new Zend_Test_DbAdapter;
        $dbAdapter->appendLastInsertIdToStack($collectionId);
        $db = new Omeka_Db($dbAdapter);
        $this->collection = new Collection($db);
        $profilerHelper = new Omeka_Test_Helper_DbProfiler($db->getAdapter()->getProfiler(), $this);

        $dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement([[3]]));

        $this->assertEquals(3, $this->collection->totalItems());
    }

    public function testHasContributorFalseBeforeSave()
    {
        $this->assertFalse($this->collection->hasContributor());
    }

    public function testHasContributorFalseAfterSave()
    {
        $this->collection->save();
        $this->assertFalse($this->collection->hasContributor());
    }

    public function testHasContributorTrueBeforeSave()
    {
        $elementTexts = ['Dublin Core' => [
            'Description' => [['text' => '', 'html' => false]],
            'Contributor' => [['text' => 'Willy', 'html' => false]],
        ]];
        $this->collection->addElementTextsByArray($elementTexts);

        // added contributors are NOT recognized until the collection is saved.
        $this->assertFalse($this->collection->hasContributor());
    }

    public function testHasContributorTrueAfterSave()
    {
        $elementTexts = ['Dublin Core' => [
            'Description' => [['text' => '', 'html' => false]],
            'Contributor' => [['text' => 'Willy', 'html' => false]],
        ]];
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();

        $this->assertEquals('Willy', metadata($this->collection, ['Dublin Core', 'Contributor']));

        $this->assertTrue($this->collection->hasContributor());
    }

    public function testAddElementTextsByArrayBeforeSave()
    {
        $titleTextBefore = 'Jerry';
        $titleTextAfter = '';
        $creatorTextBefore = '<b>Fred</b>';
        $creatorTextAfter = '';
        $descriptionTextBefore = 'A book about Jerry';
        $descriptionTextAfter = '';
        $contributorTextBefore = '<span>Willy</span> jumped high.';
        $contributorTextAfter = '';

        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => $titleTextBefore, 'html' => false]],
            'Creator' => [['text' => $creatorTextBefore, 'html' => true]],
            'Description' => [['text' => $descriptionTextBefore, 'html' => false]],
            'Contributor' => [['text' => $contributorTextBefore, 'html' => false]],
        ]];
        $this->collection->addElementTextsByArray($elementTexts);

        // element texts are NOT recognized until the collection is saved.
        $this->assertEquals($titleTextAfter, metadata($this->collection, ['Dublin Core', 'Title']));
        $this->assertEquals($creatorTextAfter, metadata($this->collection, ['Dublin Core', 'Creator']));
        $this->assertEquals($descriptionTextAfter, metadata($this->collection, ['Dublin Core', 'Description']));
        $this->assertEquals($contributorTextAfter, metadata($this->collection, ['Dublin Core', 'Contributor']));
    }

    public function testAddElementTextsByArrayAfterSave()
    {
        $titleTextBefore = 'Jerry';
        $titleTextAfter = 'Jerry';
        $creatorTextBefore = '<b>Fred</b>';
        $creatorTextAfter = '<b>Fred</b>';
        $descriptionTextBefore = 'A book about Jerry';
        $descriptionTextAfter = 'A book about Jerry';
        $contributorTextBefore = '<span>Willy</span> jumped high.';
        $contributorTextAfter = '<span>Willy</span> jumped high.';

        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => $titleTextBefore, 'html' => false]],
            'Creator' => [['text' => $creatorTextBefore, 'html' => true]],
            'Description' => [['text' => $descriptionTextBefore, 'html' => false]],
            'Contributor' => [['text' => $contributorTextBefore, 'html' => false]],
        ]];
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();

        // element texts are NOT recognized until the collection is saved.
        $this->assertEquals($titleTextAfter, metadata($this->collection, ['Dublin Core', 'Title']));
        $this->assertEquals($creatorTextAfter, metadata($this->collection, ['Dublin Core', 'Creator']));
        $this->assertEquals($descriptionTextAfter, metadata($this->collection, ['Dublin Core', 'Description']));
        $this->assertEquals($contributorTextAfter, metadata($this->collection, ['Dublin Core', 'Contributor']));
    }

    public function testValidCollectionTitle()
    {
        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => str_repeat('b', 150), 'html' => false]],
            'Description' => [['text' => '', 'html' => false]]
        ]];
        $this->collection->addElementTextsByArray($elementTexts);

        $this->assertTrue($this->collection->isValid());
    }

    public function testInsertSetsAddedDate()
    {
        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => 'foobar', 'html' => false]],
            'Description' => [['text' => '', 'html' => false]]
        ]];
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();

        $this->assertNotNull($this->collection->added);
        $this->assertThat(new Zend_Date($this->collection->added), $this->isInstanceOf('Zend_Date'),
            "'added' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }

    public function testInsertSetsModifiedDate()
    {
        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => 'foobar', 'html' => false]],
            'Description' => [['text' => '', 'html' => false]]
        ]];
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();

        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }

    public function testUpdateSetsModifiedDate()
    {
        $elementTexts = ['Dublin Core' => [
            'Title' => [['text' => 'foobar', 'html' => false]],
            'Description' => [['text' => '', 'html' => false]]
        ]];
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();

        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }

    public function testSetAddedByFailsWithNonpersistedUser()
    {
        $this->setExpectedException('RuntimeException');
        try {
            $this->collection->setAddedBy(new User($this->db));
        } catch (Exception $e) {
            $this->assertStringContainsString("unsaved user", $e->getMessage());
            throw $e;
        }
    }

    public function testSetAddedByUser()
    {
        $userId = 5;

        $user = new User($this->db);
        $user->id = $userId;
        $this->collection->setAddedBy($user);

        $this->assertEquals($userId, $this->collection->owner_id);
    }
}
