<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Installer for test cases that require database access.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Test extends Installer_Default
{    
    const TEST_ITEM_TITLE = 'Foobar';

    private $_testDefaults = array(
        'administrator_email'           => Omeka_Test_Resource_Db::SUPER_EMAIL, 
        'copyright'                     => Omeka_Test_Resource_Db::DEFAULT_COPYRIGHT, 
        'site_title'                    => Omeka_Test_Resource_Db::DEFAULT_SITE_TITLE, 
        'author'                        => Omeka_Test_Resource_Db::DEFAULT_AUTHOR, 
        'description'                   => Omeka_Test_Resource_Db::DEFAULT_DESCRIPTION, 
        'thumbnail_constraint'          => Omeka_Form_Install::DEFAULT_THUMBNAIL_CONSTRAINT, 
        'square_thumbnail_constraint'   => Omeka_Form_Install::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
        'fullsize_constraint'           => Omeka_Form_Install::DEFAULT_FULLSIZE_CONSTRAINT, 
        'per_page_admin'                => Omeka_Form_Install::DEFAULT_PER_PAGE_ADMIN, 
        'per_page_public'               => Omeka_Form_Install::DEFAULT_PER_PAGE_PUBLIC, 
        'show_empty_elements'           => Omeka_Form_Install::DEFAULT_SHOW_EMPTY_ELEMENTS,
        'path_to_convert'               => '',
        'username'                      => Omeka_Test_Resource_Db::SUPER_USERNAME,
        'password'                      => Omeka_Test_Resource_Db::SUPER_PASSWORD,
        'super_email'                   => Omeka_Test_Resource_Db::SUPER_EMAIL
    );
    
    /**
     * Overridden to retrieve values only from a predefined array.
     */
    protected function _getValue($fieldName)
    {
        if (!array_key_exists($fieldName, $this->_testDefaults)) {
            throw new Installer_Exception("Cannot find field named '$fieldName'.");
        }
        return $this->_testDefaults[$fieldName];
    }

    public function install()
    {
        parent::install();
        self::addItem($this->getDb());
    }

    public static function addItem(Omeka_Db $db)
    {
        // Keep the record objects from dying.
        Omeka_Context::getInstance()->db = $db;
        $itemBuilder = new ItemBuilder($db);
        // Item should be public to avoid weird issues with ACL integration 
        // (test must authenticate a user in order to retrieve non-public 
        // items).
        $itemBuilder->setRecordMetadata(array(
            'public' => 1,
        ));
        $itemBuilder->setElementTexts(array(
            'Dublin Core' => array(
                'Title' => array(
                    array('text' => self::TEST_ITEM_TITLE,
                          'html' => 0),
                ),
            ),
        ));
        $item = $itemBuilder->build();
        release_object($item);
    }
}
