<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 */
 
$resources = array(
    'Items', 
    'Collections', 
    'ElementSets', 
    'Files', 
    'Plugins', 
    'Settings', 
    'Security', 
    'Upgrade', 
    'Tags', 
    'Themes', 
    'SystemInfo',
    'ItemTypes', 
    'Users',
);

$acl = new Zend_Acl;

$acl->addRole('super');

// Admins inherit privileges from super users.
$acl->addRole('admin', 'super');

// Contributors inherit researcher permissions.
$acl->addRole('researcher');
$acl->addRole('contributor', 'researcher');

foreach ($resources as $resourceName) {
    $acl->addResource($resourceName);
}

// Anyone can browse Items, Item Types, Tags and Collections
$acl->allow(null,
            array('Items', 'ItemTypes', 'Tags', 'Collections'),
            array('index','browse', 'show')
);
// Anyone can browse items by tags or use advanced search for items
$acl->allow(null,
            array('Items'),
            array('tags', 'advanced-search')
);
// Super user can do anything
$acl->allow('super');
// Researchers can view items and collections that are not yet public
$acl->allow('researcher',
            array('Items', 'Collections'),
            'showNotPublic'
);
// Contributors can add and tag items, edit or delete their own items, and see their items that are not public
$acl->allow('contributor',
            'Items',
            array('tag', 'add', 'batch-edit', 'batch-edit-save',
                  'editSelf', 'deleteSelf', 'showSelfNotPublic')
);
$acl->allow('contributor', 'Tags', array('autocomplete'));
// Non-authenticated users can access the upgrade script (for logistical reasons).
$acl->allow(null, 'Upgrade');

//Deny a couple of specific privileges to admin users
$acl->deny('admin', array(
    'Settings', 
    'Plugins', 
    'Themes', 
    'ElementSets', 
    'Security', 
    'SystemInfo'
));
$acl->deny(array(null, 'researcher', 'contributor', 'admin', 'super'), 'Users');
// For some unknown reason, this assertion must be associated with named roles 
// (i.e., not null) in order to work correctly.  Allowing the null role causes 
// it to fail.
$acl->allow(array('contributor', 'researcher', 'admin', 'super'), 'Users', null,
    new User_AclAssertion());
$acl->allow(array('contributor', 'researcher', 'admin', 'super'),
    'Items', array('edit', 'delete'), new Item_OwnershipAclAssertion());
$acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));

// Because Users resource was denied to admins, it must be explicitly allowed here.
$acl->allow(array(null, 'admin'), 'Users', array('edit', 'show', 'change-password', 'delete'), new User_AclAssertion());
// Always allow users to login, logout and send forgot-password notifications.
$acl->allow(array(null, 'admin'), 'Users', array('login', 'logout', 'forgot-password', 'activate'));
?>
