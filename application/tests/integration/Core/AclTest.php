<?php

/**
* 
*/
class Core_AclTest extends PHPUnit_Framework_TestCase
{
    protected $_acl;
    
    /**
     * Include the defined ACL, exactly the way Omeka loads it by default.
     * 
     * @return void
     **/
    public function setUp()
    {
        include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
        
        $this->_acl = $acl;
    }
    
    public function testAclLoads()
    {
        $acl = $this->_acl;
        
        //Verify that contributor has proper permissions
        $this->assertTrue($acl->isAllowed('contributor', 'Items', 'add'));
        $this->assertFalse($acl->isAllowed('contributor', 'ItemTypes', 'add'));
        $this->assertFalse($acl->isAllowed('contributor', 'Plugins', 'add'));
    }
}
