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
class Omeka_Controller_CollectionsControllerTest extends Omeka_Test_AppTestCase
{
    public function testRenderAddForm()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $this->dispatch('collections/add');
        $this->assertController('collections');
        $this->assertAction('add');
        $this->assertQuery("input#name");
    }
}