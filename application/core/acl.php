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

// We have to jump through some hoops to deny Users to admins since they
// normally have all the super permissions.
$acl->deny(null, 'Users');
$acl->allow(array('super', 'admin', 'contributor', 'researcher'),
    'Users', null, new Omeka_Acl_Assert_User);
//$acl->allow(array('super', 'admin'), 'Users',
//s    array('edit', 'show', 'change-password', 'delete'), new Omeka_Acl_Assert_User);

$acl->allow(null, 'Items', array('edit', 'delete'),
    new Omeka_Acl_Assert_Ownership);

$acl->deny('admin', 'ItemTypes', array('delete', 'delete-element'));

// Always allow users to login, logout and send forgot-password notifications.
$acl->allow(array(null, 'admin'), 'Users',
    array('login', 'logout', 'forgot-password', 'activate'));
