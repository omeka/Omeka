<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for ItemAtom model class.
 *
 * @package Omeka
 */
class Omeka_Model_ItemAtomTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = false;

    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testNoContext()
    {
        $this->dispatch('items/browse');
        $this->assertNotHeaderContains('Content-Type', 'application/atom+xml; charset=utf-8');
    }

    public function testAtomContext()
    {
        $this->dispatch('items/browse?output=atom');
        $this->assertHeaderContains('Content-Type', 'application/atom+xml; charset=utf-8');
    }
    public function testGetFeedOnEmptyItem()
    {
        // Dispatching seems to be required to get the view scripts loaded
        // so show_item_metadata works in ItemAtom.
        $this->dispatch('items/browse');

        $item = new Item;
        $item->id = 100;

        $atom = new ItemAtom(array($item));

        $feed = $atom->getFeed();

        $this->_assertAtomFeed($feed);
    }

    public function testGetFeedOnItemWithFile()
    {
        $filename = 'test.jpg';
        $mimeType = 'image/jpeg';
        $size = '1024';

        // Dispatching seems to be required to get the view scripts loaded
        // so show_item_metadata works in ItemAtom.
        $this->dispatch('items/browse');

        $item = new Item;
        $item->id = 100;

        // Some sneaky stuff here to trick ItemAtom into seeing a file for
        // an item without saving them.
        $file = new File;
        $file->id = 100;
        $file->archive_filename = $filename;
        $file->setMimeType($mimeType);
        $file->size = $size;
        $item->Files = array($file);

        $atom = new ItemAtom(array($item));

        $feed = $atom->getFeed();

        $this->_assertAtomFeed($feed);

        $this->assertTag(array(
            'tag' => 'feed',
            'child' => array(
                'tag' => 'entry',
                'child' => array(
                    'tag' => 'link',
                    'attributes' => array(
                        'rel' => 'enclosure',
                        'type' => $mimeType,
                        'length' => $size,
                        'href' => $file->getWebPath('archive')
                    )
                )
            )
        ), $feed, 'The feed did not contain a correct file enclosure.', false);
    }

    private function _assertAtomFeed($feed)
    {
        return $this->assertTag(array(
            'tag' => 'feed',
            'attributes' => array(
                'xmlns' => 'http://www.w3.org/2005/Atom'
            ),
            'child' => array(
                'tag' => 'entry',
                'child' => array(
                    'tag' => 'content',
                    'attributes' => array(
                        'type' => 'html'
                    )
                )
            )
            ), $feed, 'The generated feed was not a valid Atom feed.', false);
    }
}
