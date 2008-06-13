<?php
class FileMigration extends Omeka_Db_Migration
{
    const backupTableSuffix = '__backup__21';
    
    protected $elementSets;
    
    // Element sets.
    protected $es = array(
        array('name' => 'Omeka Legacy File Elements', 'description' => 'The metadata element set that, in addition to the Dublin Core element set, was included in the `files` table in previous versions of Omeka. These elements are common to all Omeka files. This set may be deprecated in future versions.'), 
        array('name' => 'Omeka Image File Elements',  'description' => 'The metadata element set that was included in the `files_images` table in previous versions of Omeka. These elements are common to all image files.'), 
        array('name' => 'Omeka Video File Elements',  'description' => 'The metadata element set that was included in the `files_videos` table in previous versions of Omeka. These elements are common to all video files.'), 
    );
    
    public function up()
    {
        $this->renameTablesBackup(); // Always rename tables first.
        $this->createNewTables();
        $this->insertElementSets();
        /*
        $this->migrateFileElementSetLookup();
        $this->migrateFilesElements();
        $this->migrateFiles();
        */
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
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        DROP TABLE IF EXISTS `{$db->prefix}files_elements`;
        DROP TABLE IF EXISTS `{$db->prefix}file_element_set_lookup`;";
        $db->execBlock($sql);
    }

    protected function renameTablesRevert()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}files" . self::backupTableSuffix . "` TO `{$db->prefix}files`, 
        `{$db->prefix}files_images" . self::backupTableSuffix . "` TO `{$db->prefix}files_images`, 
        `{$db->prefix}files_videos" . self::backupTableSuffix . "` TO `{$db->prefix}files_videos`, 
        `{$db->prefix}file_meta_lookup" . self::backupTableSuffix . "` TO `{$db->prefix}file_meta_lookup`;";
        $db->exec($sql);
    }
    
    protected function renameTablesBackup()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}files` TO `{$db->prefix}files" . self::backupTableSuffix . "`, 
        `{$db->prefix}files_images` TO `{$db->prefix}files_images" . self::backupTableSuffix . "`, 
        `{$db->prefix}files_videos` TO `{$db->prefix}files_videos" . self::backupTableSuffix . "`, 
        `{$db->prefix}file_meta_lookup` TO `{$db->prefix}file_meta_lookup" . self::backupTableSuffix . "`;";
        $db->exec($sql);
    }

    protected function createNewTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        CREATE TABLE `{$db->prefix}files` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `item_id` int(10) unsigned NOT NULL,
          `archive_filename` text collate utf8_unicode_ci,
          `original_filename` text collate utf8_unicode_ci,
          `size` int(10) unsigned default NULL,
          `mime_browser` text collate utf8_unicode_ci,
          `mime_os` text collate utf8_unicode_ci,
          `type_os` text collate utf8_unicode_ci,
          `has_derivative_image` tinyint(1) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}files_elements`;
        CREATE TABLE `{$db->prefix}files_elements` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `file_id` int(10) unsigned NOT NULL,
          `element_id` int(10) unsigned NOT NULL,
          `text` mediumtext collate utf8_unicode_ci,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}file_element_set_lookup`;
        CREATE TABLE `{$db->prefix}file_element_set_lookup` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `element_set_id` int(10) unsigned NOT NULL,
          `mime_type` varchar(100) collate utf8_unicode_ci NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->execBlock($sql);
    }
    
    protected function insertElementSets()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}element_sets` (
            `id`, 
            `name`, 
            `description`
        ) VALUES (NULL, ?, ?)";
        foreach ($this->es as $elementSet) {
            $db->exec($sql, array($elementSet['name'], $elementSet['description']));
        }
        $this->_setElementSets();
    }
    
    protected function _setElementSets()
    {
        $db = $this->db;
        $sql = "SELECT * FROM `{$db->prefix}element_sets`";
        $this->elementSets = $db->query($sql)->fetchAll();
    }
}

/*
Which columns in `files` should be elements in the new `elements` table?
Which columns in `files` should be columns in the new `files` table?

F     = `files` columns
FESL  = `file_element_set_lookup` columns
E-DC  = built-in Dublin Core elements
E-F   = built-in legacy file elements
E-I   = built-in image elements
E-V   = built-in video elements
D     = depricated

files
--------------------------
F       id
E-DC    title
E-DC    publisher
E-DC    language
E-DC    relation
E-DC    coverage
E-DC    rights
E-DC    description
E-DC    source
E-DC    subject
E-DC    creator
E-F     additional_creator
E-DC    date
D       added
D       modified
F       item_id
E-DC    format
E-F     transcriber
E-F     producer
E-F     render_device
E-F     render_details
E-F     capture_date
E-F     capture_device
E-F     capture_details
E-F     change_history
E-F     watermark
E-F     authentication
E-F     encryption
E-F     compression
E-F     post_processing
F       archive_filename
F       original_filename
F       size
F       mime_browser
F       mime_os
F       type_os
D       lookup_id
F       has_derivative_image

+++++++
`added` and `modified` will be put into entities table, just like items

Q: where is contributor, identifier, type?
A: It doesn't matter anymore. They are included in the `elements` table.
+++++++

files_images
------------------------------
D       id
E-I     width
E-I     height
E-I     bit_depth
E-I     channels
E-I     exif_string
E-I     exif_array
E-I     iptc_string
E-I     iptc_array
D       file_id

files_videos
-------------------------------
D       id
E-V     bitrate
E-V     duration
E-V     sample_rate
E-V     codec
D       file_id
E-V     width
E-V     height

file_meta_lookup
-------------------------------
FESL     id
FESL     mime_type
D       table_name
D       table_class

================================
================================
*/
