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
class Models_Output_ItemAtomTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = false;

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
        $this->dispatch('items/browse');

        $item = new Item;
        $item->id = 100;

        $atom = new Output_ItemAtom(array($item));

        $feed = $atom->getFeed();
        $dom = new Zend_Dom_Query($feed);

        $this->_assertAtomFeed($dom);
    }

    public function testGetFeedOnItemWithFile()
    {
        $filename = 'test.jpg';
        $mimeType = 'image/jpeg';
        $size = '1024';

        // Dispatching seems to be required to get the view scripts loaded
        $this->dispatch('items/browse');

        $item = new Item;
        $item->id = 100;

        // Some sneaky stuff here to trick ItemAtom into seeing a file for
        // an item without saving them.
        $file = new File;
        $file->id = 100;
        $file->filename = $filename;
        $file->mime_type = $mimeType;
        $file->size = $size;
        $item->Files = array($file);

        $atom = new Output_ItemAtom(array($item));

        $feed = $atom->getFeed();

        $dom = new Zend_Dom_Query($feed);
        $this->_assertAtomFeed($dom);
        $href = $file->getWebPath('original');
        $queryResult = $dom->queryXpath("/atom:feed/atom:entry/atom:link[@rel='enclosure' and @type='$mimeType' and @length='$size' and @href='$href']");
        $this->assertCount(1, $queryResult, 'The feed did not contain a correct file enclosure.');
    }

    private function _assertAtomFeed($dom)
    {
        $dom->registerXpathNamespaces(array('atom' => 'http://www.w3.org/2005/Atom'));
        $queryResult = $dom->queryXpath("/atom:feed/atom:entry/atom:content[@type='html']");
        $this->assertCount(1, $queryResult, 'The generated feed was not a valid Atom feed.');
    }
}
