<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_View_Helper_MediaTest extends PHPUnit_Framework_TestCase
{
    private $mimeTypeCallbackFired = false;

    public function setUp()
    {
        $this->view = $this->getMock('Omeka_View', array(), array(), '', false);
        $this->helper = new Omeka_View_Helper_FileMarkup;
        $this->file = $this->getMock('File', array(), array(), '', false);
    }
    
    public function testAddMimeTypes()
    {
        add_file_display_callback(array('image/jpeg'), 
                              array($this, 'mimeTypeCallback'), 
                              array('foo' => 'bar'));
                              
        // File should be of the 'image/jpeg' type.
        $this->file->mime_type = 'image/jpeg';
        $this->file->stored = '1';

        $this->helper->fileMarkup($this->file);  
        $this->assertTrue($this->mimeTypeCallbackFired, "MIME type callback should have been fired.");                                                   
    }
    
    public function mimeTypeCallback($file, $options)
    {
        $this->mimeTypeCallbackFired = true;
    }
    
    public function __sleep()
    {
        return array('helper');
    }
}
