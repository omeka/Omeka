<?php
class DcRewrite extends Omeka_Db_Migration
{
    const backupTableSuffix = '__backup__19';
    
    protected $elementSets;
    protected $elementTypes;
    protected $elementSetElements;
    
    // Array containing the new element sets. file_image, and file_video will be 
    // added in a later migration. Do not change the order of these elements.
    protected $es = array(
        array('name' => 'Dublin Core Metadata Element Set', 'description' => 'The Dublin Core metadata element set. These elements are common to all Omeka resourses, including items, files, collections, exhibits, and entities. See http://dublincore.org/documents/dces/.'), 
        array('name' => 'Omeka Legacy Elements',            'description' => 'The metadata element set that, in addition to the Dublin Core element set, was included in the `items` table in previous versions of Omeka. These elements are common to all Omeka items. This set may be deprecated in future versions.')
    );
    
    // Array containing the new element types.
    protected $et = array(
        array('name' => 'text',      'regular_expression' => '/(?<!.).{0,65535}(?!.)/s', 'description' => 'A long, typically multi-line text string. Up to 65535 characters. Renders a textarea in an HTML form.'), 
        array('name' => 'tinytext',  'regular_expression' => '/(?<!.).{0,255}(?!.)/s', 'description' => 'A short, one-line text string. Up to 255 characters. Renders a text input in a HTML form.'), 
        array('name' => 'daterange', 'regular_expression' => '/^(?:\-?[0-9]{1,9}(?:\-\b(?:0[1-9]|1[0-2])\b(?:\-\b(?:0[1-9]|[1-2][0-9]|3[0-1])\b)?)?)?(?: ?(?:\-?[0-9]{1,9}(?:\-\b(?:0[1-9]|1[0-2])\b(?:\-\b(?:0[1-9]|[1-2][0-9]|3[0-1])\b)?)?)?)?$/', 'description' => 'A date range, begin to end. In format yyyy-mm-dd yyyy-mm-dd: 
    * No hyphen before a year indicates C.E.
    * A hyphen before a year indicates B.C.E.
    * At least one year must exist (begin and/or end date)
    * Months are optional
    * Days are optional
    * Months must precede days
    * Years must precede months
    * The years must be between -999,999,999 and 999,999,999
    * The months must be between 01 and 12 (zerofill)
    * The days must be between 01 and 31 (zerofill)
    * A space character separates the begin and end dates
    * The begin or end date may be ommitted
    * If the begin date is ommitted a space character precedes the end date
There are some bugs in this regex:
    * A match on "" (empty string) results true
    * A match on "0" (zero) results true (there is no year zero)'), 
    );
    
    // Array containing the Dublin Core and miscellaneous elements taken from the 
    // original `items` table. Elements without originalNames do not exist in the 
    // original `items` table and are being added here for the first time. Note 
    // that `temporal_coverage_start` and `temporal_coverage_end` are combined into 
    // "Temporal Coverage".
    protected $e = array(
        // Built-in Dublin Core elements:
        array('name' => 'Contributor', '_originalName' => 'contributor', 'order' => 8,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'An entity responsible for making contributions to the resource. Examples of a Contributor include a person, an organization, or a service. Typically, the name of a Contributor should be used to indicate the entity.'), 
        array('name' => 'Coverage',    '_originalName' => null,          'order' => 15, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant. Spatial topic and spatial applicability may be a named place or a location specified by its geographic coordinates. Temporal topic may be a named period, date, or date range. A jurisdiction may be a named administrative entity or a geographic place to which the resource applies. Recommended best practice is to use a controlled vocabulary such as the Thesaurus of Geographic Names [TGN]. Where appropriate, named places or time periods can be used in preference to numeric identifiers such as sets of coordinates or date ranges.'), 
        array('name' => 'Creator',     '_originalName' => 'creator',     'order' => 4,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'An entity primarily responsible for making the resource. Examples of a Creator include a person, an organization, or a service. Typically, the name of a Creator should be used to indicate the entity.'), 
        array('name' => 'Date',        '_originalName' => 'date',        'order' => 7,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'A point or period of time associated with an event in the lifecycle of the resource. Date may be used to express temporal information at any level of granularity. Recommended best practice is to use an encoding scheme, such as the W3CDTF profile of ISO 8601 [W3CDTF].'), 
        array('name' => 'Description', '_originalName' => 'description', 'order' => 3,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'An account of the resource. Description may include but is not limited to: an abstract, a table of contents, a graphical representation, or a free-text account of the resource.'), 
        array('name' => 'Format',      '_originalName' => 'format',      'order' => 11, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'The file format, physical medium, or dimensions of the resource. Examples of dimensions include size and duration. Recommended best practice is to use a controlled vocabulary such as the list of Internet Media Types [MIME].'), 
        array('name' => 'Identifier',  '_originalName' => null,          'order' => 14, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'An unambiguous reference to the resource within a given context. Recommended best practice is to identify the resource by means of a string conforming to a formal identification system.'), 
        array('name' => 'Language',    '_originalName' => 'language',    'order' => 12, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'A language of the resource. Recommended best practice is to use a controlled vocabulary such as RFC 4646 [RFC4646].'), 
        array('name' => 'Publisher',   '_originalName' => 'publisher',   'order' => 6,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'An entity responsible for making the resource available. Examples of a Publisher include a person, an organization, or a service. Typically, the name of a Publisher should be used to indicate the entity.'), 
        array('name' => 'Relation',    '_originalName' => 'relation',    'order' => 10, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'A related resource. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.'), 
        array('name' => 'Rights',      '_originalName' => 'rights',      'order' => 9,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'Information about rights held in and over the resource. Typically, rights information includes a statement about various property rights associated with the resource, including intellectual property rights.'), 
        array('name' => 'Source',      '_originalName' => 'source',      'order' => 5,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'A related resource from which the described resource is derived. The described resource may be derived from the related resource in whole or in part. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.'), 
        array('name' => 'Subject',     '_originalName' => 'subject',     'order' => 2,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'The topic of the resource. Typically, the subject will be represented using keywords, key phrases, or classification codes. Recommended best practice is to use a controlled vocabulary. To describe the spatial or temporal topic of the resource, use the Coverage element.'), 
        array('name' => 'Title',       '_originalName' => 'title',       'order' => 1,  'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'A name given to the resource. Typically, a Title will be a name by which the resource is formally known.'), 
        array('name' => 'Type',        '_originalName' => null,          'order' => 13, 'element_set_id' => 1, 'elementTypeName' => 'text', 'description' => 'The nature or genre of the resource. Recommended best practice is to use a controlled vocabulary such as the DCMI Type Vocabulary [DCMITYPE]. To describe the file format, physical medium, or dimensions of the resource, use the Format element.'), 
        // Miscellaneous elements that are in a "built-in" element set for legacy reasons:
        array('name' => 'Spatial Coverage',   '_originalName' => 'spatial_coverage',   'order' => 3, 'element_set_id' => 2, 'elementTypeName' => 'text',      'description' => ''), 
        array('name' => 'Additional Creator', '_originalName' => 'additional_creator', 'order' => 1, 'element_set_id' => 2, 'elementTypeName' => 'text',      'description' => ''), 
        array('name' => 'Rights Holder',      '_originalName' => 'rights_holder',      'order' => 2, 'element_set_id' => 2, 'elementTypeName' => 'text',      'description' => ''), 
        array('name' => 'Provenance',         '_originalName' => 'provenance',         'order' => 5, 'element_set_id' => 2, 'elementTypeName' => 'text',      'description' => ''), 
        array('name' => 'Citation',           '_originalName' => 'citation',           'order' => 6, 'element_set_id' => 2, 'elementTypeName' => 'text',      'description' => ''), 
        array('name' => 'Temporal Coverage',  '_originalName' => null,                 'order' => 4, 'element_set_id' => 2, 'elementTypeName' => 'daterange', 'description' => ''), 
    );
    
    // Mapping array containing the column names of the old `items` table (keys) 
    // and new field names in the new `elements` table (values). Note that `temporal_coverage_start` 
    // and `temporal_coverage_end` are combined into "Temporal Coverage". This is 
    // a convienence array used in self::_insertItemsElementsFromItems(). 
    protected $eMap = array(
        // Built-in Dublin Core elements:
        'contributor' => 'Contributor', 
        'creator'     => 'Creator', 
        'date'        => 'Date', 
        'description' => 'Description', 
        'format'      => 'Format', 
        'language'    => 'Language', 
        'publisher'   => 'Publisher', 
        'relation'    => 'Relation', 
        'rights'      => 'Rights', 
        'source'      => 'Source', 
        'subject'     => 'Subject', 
        'title'       => 'Title', 
        // Miscellaneous elements that are in a "built-in" element set for legacy 
        // reasons:
        'spatial_coverage'   => 'Spatial Coverage', 
        'additional_creator' => 'Additional Creator', 
        'rights_holder'      => 'Rights Holder', 
        'provenance'         => 'Provenance', 
        'citation'           => 'Citation', 
        null                 => 'Temporal Coverage'
    );
    
    public function up()
    {
        $this->renameTablesBackup(); // Always rename tables first.
        $this->createNewTables();
        $this->migrateElementSets();
        $this->migrateElementTypes();
        $this->migrateElements();
        $this->migrateItemTypes();
        $this->migrateItemTypesElements();
        $this->migrateItemsElements();
        $this->migrateItems();
    }
    
    public function down()
    {
        $this->dropTables(); // Always drop the tables first.
        $this->renameTablesRevert();
    }
    
    protected function dropTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}elements`;
        DROP TABLE IF EXISTS `{$db->prefix}element_sets`;
        DROP TABLE IF EXISTS `{$db->prefix}element_types`;
        DROP TABLE IF EXISTS `{$db->prefix}items`;
        DROP TABLE IF EXISTS `{$db->prefix}items_elements`;
        DROP TABLE IF EXISTS `{$db->prefix}item_types`;
        DROP TABLE IF EXISTS `{$db->prefix}item_types_elements`;";
        $db->execBlock($sql);
    }
    
    protected function renameTablesRevert()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}items" . self::backupTableSuffix . "` TO `{$db->prefix}items`, 
        `{$db->prefix}metafields" . self::backupTableSuffix . "` TO `{$db->prefix}metafields`, 
        `{$db->prefix}metatext" . self::backupTableSuffix . "` TO `{$db->prefix}metatext`, 
        `{$db->prefix}types" . self::backupTableSuffix . "` TO `{$db->prefix}types`, 
        `{$db->prefix}types_metafields" . self::backupTableSuffix . "` TO `{$db->prefix}types_metafields`;";
        $db->exec($sql);
    }

    protected function renameTablesBackup()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}items` TO `{$db->prefix}items" . self::backupTableSuffix . "`, 
        `{$db->prefix}metafields` TO `{$db->prefix}metafields" . self::backupTableSuffix . "`, 
        `{$db->prefix}metatext` TO `{$db->prefix}metatext" . self::backupTableSuffix . "`, 
        `{$db->prefix}types` TO `{$db->prefix}types" . self::backupTableSuffix . "`, 
        `{$db->prefix}types_metafields` TO `{$db->prefix}types_metafields" . self::backupTableSuffix . "`;";
        $db->exec($sql);
    }
    
    // Create new tables. Note that the `items` table is named `items_temp` 
    // until the old `items` table is dropped. This is because the original 
    // `items` table is needed until this migration is complete.
    protected function createNewTables()
    {
        $db = $this->db;
        
        // To do: add indexes, unique indexes, and fulltext indexes:
        // unique index on elements.name
        
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}elements`;
        CREATE TABLE `{$db->prefix}elements` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `element_type_id` int(10) unsigned NOT NULL,
          `element_set_id` int(10) unsigned default NULL,
          `plugin_id` int(10) unsigned default NULL,
          `name` varchar(100) collate utf8_unicode_ci NOT NULL,
          `description` text collate utf8_unicode_ci,
          `order` int(10) unsigned default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}element_sets`;
        CREATE TABLE `{$db->prefix}element_sets` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `name` varchar(100) collate utf8_unicode_ci NOT NULL,
          `description` text collate utf8_unicode_ci,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}element_types`;
        CREATE TABLE `{$db->prefix}element_types` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `name` varchar(100) collate utf8_unicode_ci NOT NULL,
          `description` text collate utf8_unicode_ci,
          `regular_expression` text collate utf8_unicode_ci,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}items`;
        CREATE TABLE `{$db->prefix}items` (
          `id` int UNSIGNED NOT NULL auto_increment,
          `item_type_id` int UNSIGNED default NULL,
          `collection_id` int UNSIGNED default NULL,
          `featured` tinyint(1) NOT NULL,
          `public` tinyint(1) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}items_elements`;
        CREATE TABLE `{$db->prefix}items_elements` (
          `id` int UNSIGNED NOT NULL auto_increment,
          `item_id` int UNSIGNED NOT NULL,
          `element_id` int UNSIGNED NOT NULL,
          `text` mediumtext collate utf8_unicode_ci,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}item_types`;
        CREATE TABLE `{$db->prefix}item_types` (
          `id` int UNSIGNED NOT NULL auto_increment,
          `plugin_id` int UNSIGNED default NULL,
          `name` varchar(100) collate utf8_unicode_ci NOT NULL,
          `description` text collate utf8_unicode_ci,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}item_types_elements`;
        CREATE TABLE `{$db->prefix}item_types_elements` (
          `id` int UNSIGNED NOT NULL auto_increment,
          `item_type_id` int UNSIGNED NOT NULL,
          `element_id` int UNSIGNED NOT NULL,
          `plugin_id` int UNSIGNED default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        
        $db->execBlock($sql);
    }
    
    // Migrate data from the $es property to the `element_sets` table.
    protected function migrateElementSets()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}element_sets` (
            `id`, 
            `name`, 
            `description`
        ) VALUES (?, ?, ?)";
        foreach ($this->es as $elementSet) {
            $id= null;
            $name= $elementSet['name'];
            $description= $elementSet['description'];
            $db->exec($sql, array($id, $name, $description));
        }
        $this->_setElementSets();
    }
    
    // Set the $elementSets property with data from the new `element_sets` table.
    protected function _setElementSets()
    {
        $db = $this->db;
        $sql = "SELECT * FROM `{$db->prefix}element_sets`";
        $this->elementSets = $db->query($sql)->fetchAll();
    }

    // Migrate data from the $et property to the `element_types` table.
    protected function migrateElementTypes()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}element_types` (
            `id`, 
            `name`, 
            `description`, 
            `regular_expression`
        ) VALUES (?, ?, ?, ?)";
        foreach ($this->et as $elementType) {
            $id                = null;
            $name              = $elementType['name'];
            $description       = $elementType['description'];
            $regularExpression = $elementType['regular_expression'];
            $db->exec($sql, array($id, $name, $description, $regularExpression));
        }
        $this->_setElementTypes();
    }
    
    // Set the $elementTypes property with data from the new `element_types` table.
    protected function _setElementTypes()
    {
        $db = $this->db;
        $sql = "SELECT * FROM `{$db->prefix}element_types`";
        $this->elementTypes = $db->query($sql)->fetchAll();
    }
    
    // Migrate data to the `elements` table.
    protected function migrateElements()
    {
        $this->_insertMetafieldElements();
        $this->_insertElementSetElements();
        $this->_setElementSetElements();
    }
    
    // Insert the elements from the old `metafields` table
    protected function _insertMetafieldElements()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}elements` (
            `id`, 
            `element_type_id`, 
            `element_set_id`, 
            `plugin_id`, 
            `name`, 
            `description`
        ) 
        SELECT 
            `id`, 
            1, 
            NULL, 
            `plugin_id`, 
            `name`, 
            `description` 
        FROM `{$db->prefix}metafields" . self::backupTableSuffix . "`;";
        $this->exec($sql);
    }

    // Insert the elements that are in an element set (Dublin Core and legacy item) 
    // from the original `items` table.
    protected function _insertElementSetElements()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}elements` (
            `id`, 
            `element_type_id`, 
            `element_set_id`, 
            `plugin_id`, 
            `name`, 
            `description`, 
            `order`
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        foreach ($this->e as $element) {
            $id              = null;
            $element_type_id = $this->_getElementTypeIdByElementTypeName($element['elementTypeName']);
            $element_set_id  = $element['element_set_id'];
            $plugin_id       = null;
            $name            = $element['name'];
            $description     = $element['description'];
            $order           = $element['order'];
            $db->exec($sql, array($id, $element_type_id, $element_set_id, $plugin_id, $name, $description, $order));
        }
    }
    
    protected function _getElementTypeIdByElementTypeName($elementTypeName)
    {
        foreach ($this->elementTypes as $elementType) {
            if ($elementType['name'] == $elementTypeName) return $elementType['id'];
        }
        return 0;
    }
    
    protected function _setElementSetElements()
    {
        $db = $this->db;
        $sql = "
        SELECT * 
        FROM `{$db->prefix}elements` 
        WHERE `element_set_id` = 1 
        OR `element_set_id` = 2";
        $this->elementSetElements = $db->query($sql)->fetchAll();
    }
        
    // Migrate data from the old `types` table to the new `item_types` table.
    protected function migrateItemTypes()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}item_types` (
            `id`, 
            `plugin_id`, 
            `name`, 
            `description`
        ) 
        SELECT 
            `id`, 
            `plugin_id`, 
            `name`, 
            `description` 
        FROM `{$db->prefix}types" . self::backupTableSuffix . "`;";
        $this->exec($sql);
    }
    
    // Migrate data from the old `types_metafields` table to the new `item_types_elements` table.
    protected function migrateItemTypesElements()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}item_types_elements` (
            `id`, 
            `item_type_id`, 
            `element_id`, 
            `plugin_id` 
        ) 
        SELECT 
            `id`, 
            `type_id`, 
            `metafield_id`, 
            `plugin_id` 
        FROM `{$db->prefix}types_metafields" . self::backupTableSuffix . "`";
        $this->exec($sql);
    }
    
    // Migrate data from the old `metatexts` table to the new `items_elements` table.
    protected function migrateItemsElements()
    {
        $this->_insertItemsElementsFromMetatexts();
        $this->_insertItemsElementsFromItems();
    }
    
    // Insert the items_elements from the `metatexts` table.
    protected function _insertItemsElementsFromMetatexts()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}items_elements` (
            `id`, 
            `item_id`, 
            `element_id`, 
            `text`
        ) 
        SELECT 
            `id`, 
            `item_id`, 
            `metafield_id`, 
            `text`
        FROM `{$db->prefix}metatext" . self::backupTableSuffix . "` 
        WHERE `text` IS NOT NULL 
        AND `text` != '';";
        $this->exec($sql);
    }
    
    protected function _insertItemsElementsFromItems()
    {
        $db = $this->db;
        
        // Get all item IDs from the old `items` table
        $sql = "SELECT `id` FROM `{$db->prefix}items" . self::backupTableSuffix . "`";
        $items = $db->query($sql)->fetchAll();
             
        // Loop through the elements that are in an element set.
        foreach ($this->elementSetElements as $elementSetElement) {
            
            // Set the original element name, which is the column name in the old 
            // `items` table
            if ($originalElementName = array_search($elementSetElement['name'], $this->eMap)) {
                
                $sql = "
                INSERT INTO `{$db->prefix}items_elements` (
                    `id`, 
                    `item_id`, 
                    `element_id`, 
                    `text`
                ) 
                SELECT 
                    NULL, 
                    ?, 
                    {$elementSetElement['id']}, 
                    `$originalElementName` 
                FROM `{$db->prefix}items" . self::backupTableSuffix . "` 
                WHERE `id` = ? 
                AND `$originalElementName` IS NOT NULL 
                AND `$originalElementName` != ''";
                
                // Loop through the items
                foreach ($items as $item) {
                    $db->exec($sql, array($item['id'], $item['id']));
                }
            
            // ADD LOGIC TO HANDLE SPECIAL CASE: CONCAT `temporal_coverage_start` 
            // and `temporal_coverage_end` into "Temporal Coverage"). The element 
            // type regex is /([\d]{4}-[\d]{4}-[\d]{4})? ([\d]{4}-[\d]{4}-[\d]{4})?/
            } elseif ($elementSetElement['name'] == 'Temporal Coverage') {
                $sql = "
                INSERT INTO `{$db->prefix}items_elements` (
                    `id`, 
                    `item_id`, 
                    `element_id`, 
                    `text`
                ) 
                SELECT 
                    NULL, 
                    ?, 
                    {$elementSetElement['id']}, 
                    CONCAT_WS('', `temporal_coverage_start`, ' ',`temporal_coverage_end`) 
                FROM `{$db->prefix}items" . self::backupTableSuffix . "` 
                WHERE `id` = ? 
                AND (
                    (
                        `temporal_coverage_start` IS NOT NULL 
                        AND `temporal_coverage_start` != ''
                        AND `temporal_coverage_start` != '0'
                        AND `temporal_coverage_start` != '0000-00-00'
                    ) OR (
                        `temporal_coverage_end` IS NOT NULL 
                        AND `temporal_coverage_end` != ''
                        AND `temporal_coverage_end` != '0'
                        AND `temporal_coverage_end` != '0000-00-00'
                    )
                )";
                
                // Loop through the items
                foreach ($items as $item) {
                    $db->exec($sql, array($item['id'], $item['id']));
                }
            }
        }
    }
    
    protected function migrateItems()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}items` (
            `id`, 
            `item_type_id`, 
            `collection_id`, 
            `featured`, 
            `public` 
        ) 
        SELECT 
            `id`, 
            `type_id`, 
            `collection_id`, 
            `featured`, 
            `public` 
        FROM `{$db->prefix}items" . self::backupTableSuffix . "`";
        $this->exec($sql);
    }
}
