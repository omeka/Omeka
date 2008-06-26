<?php
class FileMigration extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__21';
    
    // Element set names.
    const ELEMENT_SET_DUBLIN_CORE_METADATA_ELEMENT_SET = 'Dublin Core Metadata Element Set';
    const ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS = 'Omeka Legacy File Elements';
    const ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS  = 'Omeka Image File Elements';
    const ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS  = 'Omeka Video File Elements';
    
    // Element type names.
    const ELEMENT_TYPE_TEXT      = 'text';
    const ELEMENT_TYPE_TINYTEXT  = 'tinytext';
    const ELEMENT_TYPE_DATERANGE = 'daterange';
    const ELEMENT_TYPE_DATE      = 'date';
    const ELEMENT_TYPE_INTEGER   = 'integer';
    const ELEMENT_TYPE_BOOLEAN   = 'boolean';
    const ELEMENT_TYPE_DATETIME  = 'datetime';
    
    // Arrays containing all rows of a particular table.
    private $_elementSets;
    private $_elementTypes;
    
    // New element sets. Note: if these names change, be sure to modify the code 
    // in $this->_mapElementSets().
    private $_es = array(
        array('name' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'description' => 'The metadata element set that, in addition to the Dublin Core element set, was included in the `files` table in previous versions of Omeka. These elements are common to all Omeka files. This set may be deprecated in future versions.'), 
        array('name' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS,  
              'description' => 'The metadata element set that was included in the `files_images` table in previous versions of Omeka. These elements are common to all image files.'), 
        array('name' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS,  
              'description' => 'The metadata element set that was included in the `files_videos` table in previous versions of Omeka. These elements are common to all video files.'), 
    );
    
    // New elements.
    private $_e = array(
        // Omeka Legacy File Elements
        array('name' => 'Additional Creator', 
              'order' => 1,
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Transcriber', 
              'order' => 2, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Producer', 
              'order' => 3, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Render Device', 
              'order' => 4, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Render Details', 
              'order' => 5, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Capture Date', 
              'order' => 6, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_DATETIME, 
              'description' => ''), 
        array('name' => 'Capture Device', 
              'order' => 7, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Capture Details', 
              'order' => 8, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Change History', 
              'order' => 9, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Watermark', 
              'order' => 10, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Authentication', 
              'order' => 11, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Encryption', 
              'order' => 12, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Compression', 
              'order' => 13, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Post Processing', 
              'order' => 14, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        // Omeka Image File Elements
        array('name' => 'Width', 
              'order' => 1, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Height', 
              'order' => 2, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Bit Depth', 
              'order' => 3, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Channels', 
              'order' => 4, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Exif String', 
              'order' => 5, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Exif Array', 
              'order' => 6, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'IPTC String', 
              'order' => 7, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'IPTC Array', 
              'order' => 8, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        // Omeka Video File Elements
        array('name' => 'Bitrate', 
              'order' => 1, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Duration', 
              'order' => 2, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Sample Rate', 
              'order' => 3, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Codec', 
              'order' => 4, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_TEXT, 
              'description' => ''), 
        array('name' => 'Width', 
              'order' => 5, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => 'Height', 
              'order' => 6, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS, 
              'elementTypeName' => self::ELEMENT_TYPE_INTEGER, 
              'description' => ''), 
    );
    
    // Helper data mapping arrays.
    private $_esMap;
    
    // Omeka Dublin Core and legacy file elements
    private $_eMapFile = array('title'      => 'Title', 
                               'publisher'  => 'Publisher', 
                               'language'   => 'Language', 
                               'relation'   => 'Relation', 
                               'coverage'   => 'Coverage', 
                               'rights'     => 'Rights', 
                               'description'=> 'Description', 
                               'source'     => 'Source', 
                               'subject'    => 'Subject', 
                               'creator'    => 'Creator', 
                               'date'       => 'Date', 
                               'format'     => 'Format', 
                               'additional_creator' => 'Additional Creator', 
                               'transcriber'        => 'Transcriber', 
                               'producer'           => 'Producer', 
                               'render_device'      =>'Render Device', 
                               'render_details'     => 'Render Details', 
                               'capture_date'       => 'Capture Date', 
                               'capture_device'     => 'Capture Device', 
                               'capture_details'    => 'Capture Details', 
                               'change_history'     => 'Change History', 
                               'watermark'          => 'Watermark', 
                               'authentication'     => 'Authentication', 
                               'encryption'         => 'Encryption', 
                               'compression'        => 'Compression', 
                               'post_processing'    => 'Post Processing');
    // Omeka Image File Elements
    private $_eMapImage = array('width'       => 'Width', 
                                'height'      => 'Height', 
                                'bit_depth'   => 'Bit Depth', 
                                'channels'    => 'Channels', 
                                'exif_string' => 'Exif String', 
                                'exif_array'  => 'Exif Array', 
                                'iptc_string' => 'IPTC String', 
                                'iptc_array'  => 'IPTC Array');
    // Omeka Video File Elements
    private $_eMapVideo = array('bitrate'     => 'Bitrate', 
                                'duration'    => 'Duration', 
                                'sample_rate' => 'Sample Rate', 
                                'codec'       => 'Codec', 
                                'width'       => 'Width', 
                                'height'      => 'Height');
    
    public function up()
    {
        // Rename the tables for backup
        $this->_renameTablesBackup();
        // Create new tables
        $this->_createNewTables();
        
        // Insert the new element sets
        $this->_insertElementSets();
        // Set all the element sets
        $this->_setElementSets();
        // Map the element sets
        $this->_mapElementSets();
        
        // Migrate data into `file_element_set_lookup`
        $this->_migrateFileElementSetLookup();
        
        // Set all the element types
        $this->_setElementTypes();
        
        // Migrate data into `elements`
        $this->_migrateElements();
        
        // Migrate data into `files_elements`
        $this->_migrateFilesElements();
        
        /*
        // Migrate data into `files
        $this->migrateFiles();`
        */
    }
    
    public function down()
    {
        $this->_dropTables();
        $this->_renameTablesRevert();
    }
    
    protected function _renameTablesBackup()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}files` TO `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "`, 
        `{$db->prefix}files_images` TO `{$db->prefix}files_images" . self::BACKUP_TABLE_SUFFIX . "`, 
        `{$db->prefix}files_videos` TO `{$db->prefix}files_videos" . self::BACKUP_TABLE_SUFFIX . "`, 
        `{$db->prefix}file_meta_lookup` TO `{$db->prefix}file_meta_lookup" . self::BACKUP_TABLE_SUFFIX . "`;";
        $db->exec($sql);
    }
    
    private function _createNewTables()
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
    
    private function _insertElementSets()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}element_sets` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_es as $elementSet) {
            $db->exec($sql, array($elementSet['name'], $elementSet['description']));
        }
    }
    
    private function _setElementSets()
    {
        $db = $this->db;
        $sql = "SELECT * FROM `{$db->prefix}element_sets`";
        $this->_elementSets = $db->query($sql)->fetchAll();
    }
    
    protected function _mapElementSets()
    {
        $esMap = array();
        foreach ($this->_elementSets as $elementSet) {
            switch ($elementSet['name']) {
                case self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS:
                    $esMap[$elementSet['id']] = 'FilesImages';
                    break;
                case self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS:
                    $esMap[$elementSet['id']] = 'FilesVideos';
                    break;
            }
        }
        $this->_esMap = $esMap;
    }
    
    private function _migrateFileElementSetLookup()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}file_element_set_lookup` (
            `element_set_id`, 
            `mime_type`
        ) 
        SELECT 
            CASE `table_class` 
                WHEN 'FilesImages' THEN " . array_search('FilesImages', $this->_esMap) . "
                WHEN 'FilesVideos' THEN " . array_search('FilesVideos', $this->_esMap) . " 
            END, 
            `mime_type`
        FROM `{$db->prefix}file_meta_lookup" . self::BACKUP_TABLE_SUFFIX . "`";
        $db->exec($sql);
    }
    
    private function _setElementTypes()
    {
        $db = $this->db;
        $sql = "SELECT * FROM `{$db->prefix}element_types`";
        $this->_elementTypes = $db->query($sql)->fetchAll();
    }
    
    private function _migrateElements()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}elements` (
            `element_type_id`, 
            `element_set_id`, 
            `name`, 
            `description`, 
            `order`
        ) VALUES (?, ?, ?, ?, ?)";
        foreach ($this->_e as $element) {
            $elementTypeId = $this->_getElementTypeIdByElementTypeName($element['elementTypeName']);
            $elementSetId  = $this->_getElementSetIdByElementSetName($element['elementSetName']);
            $name          = $element['name'];
            $description   = $element['description'];
            $order         = $element['order'];
            $db->exec($sql, array($elementTypeId, $elementSetId, $name, $description, $order));
        }
    }
    
    private function _getElementTypeIdByElementTypeName($elementTypeName)
    {
        foreach ($this->_elementTypes as $elementType) {
            if ($elementType['name'] == $elementTypeName) {
                return $elementType['id'];
            }
        }
        return 0;
    }
    
    private function _getElementSetIdByElementSetName($elementSetName)
    {
        foreach ($this->_elementSets as $elementSet) {
            if ($elementSet['name'] == $elementSetName) {
                return $elementSet['id'];
            }
        }
        return 0;
    }
    
    private function _migrateFilesElements()
    {
        $db = $this->db;
        
        // Get all file IDs.
        $sql = "SELECT `id` FROM `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "`";
        $files = $db->query($sql)->fetchAll();
        
        // Get all Dublin Core and Omeka legacy file elements from the database.
        $dcElements = $this->_getElementsByElementSetName(self::ELEMENT_SET_DUBLIN_CORE_METADATA_ELEMENT_SET);
        $legacyElements = $this->_getElementsByElementSetName(self::ELEMENT_SET_OMEKA_LEGACY_FILE_ELEMENTS);
        $elements = array_merge($dcElements, $legacyElements);
        // Iterate through the elements.
        foreach ($elements as $element) {
            // Continue processing if the element exists in the mapping array.
            if ($originalElementName = array_search($element['name'], $this->_eMapFile)) {
                $sql = "
                INSERT INTO `{$db->prefix}files_elements` (
                    `file_id`, 
                    `element_id`, 
                    `text`
               ) 
               SELECT 
                    ?, 
                    {$element['id']}, 
                    `$originalElementName`
                FROM `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` 
                WHERE `id` = ?
                AND `$originalElementName` IS NOT NULL 
                AND `$originalElementName` != ''";
                // Loop through the items
                foreach ($files as $file) {
                    $db->exec($sql, array($file['id'], $file['id']));
                }
            }
        }
        
        // image file elements from `files_images`
        $sql = "SELECT `file_id` FROM `{$db->prefix}files_images" . self::BACKUP_TABLE_SUFFIX . "`";
        $files = $db->query($sql)->fetchAll();
        $elements = $this->_getElementsByElementSetName(self::ELEMENT_SET_OMEKA_IMAGE_FILE_ELEMENTS);
        foreach ($elements as $element) {
            // Continue processing if the element exists in the mapping array.
            if ($originalElementName = array_search($element['name'], $this->_eMapImage)) {
                $sql = "
                INSERT INTO `{$db->prefix}files_elements` (
                    `file_id`, 
                    `element_id`, 
                    `text`
               ) 
               SELECT 
                    ?, 
                    {$element['id']}, 
                    `$originalElementName`
                FROM `{$db->prefix}files_images" . self::BACKUP_TABLE_SUFFIX . "` 
                WHERE `file_id` = ?
                AND `$originalElementName` IS NOT NULL 
                AND `$originalElementName` != ''";
                // Loop through the items
                foreach ($files as $file) {
                    $db->exec($sql, array($file['file_id'], $file['file_id']));
                }
            }
        }
        
        // video file elements from `files_videos`
        $sql = "SELECT `file_id` FROM `{$db->prefix}files_videos" . self::BACKUP_TABLE_SUFFIX . "`";
        $files = $db->query($sql)->fetchAll();
        $elements = $this->_getElementsByElementSetName(self::ELEMENT_SET_OMEKA_VIDEO_FILE_ELEMENTS);
        foreach ($elements as $element) {
            // Continue processing if the element exists in the mapping array.
            if ($originalElementName = array_search($element['name'], $this->_eMapVideo)) {
                $sql = "
                INSERT INTO `{$db->prefix}files_elements` (
                    `file_id`, 
                    `element_id`, 
                    `text`
               ) 
               SELECT 
                    ?, 
                    {$element['id']}, 
                    `$originalElementName`
                FROM `{$db->prefix}files_videos" . self::BACKUP_TABLE_SUFFIX . "` 
                WHERE `file_id` = ?
                AND `$originalElementName` IS NOT NULL 
                AND `$originalElementName` != ''";
                // Loop through the items
                foreach ($files as $file) {
                    $db->exec($sql, array($file['file_id'], $file['file_id']));
                }
            }
        }
    }
    
    private function _getElementsByElementSetName($elementSetName)
    {
        $db = $this->db;
        $sql = "
        SELECT e.*
        FROM `{$db->prefix}elements` e 
        INNER JOIN `{$db->prefix}element_sets` es
        ON e.`element_set_id` = es.`id` 
        WHERE es.`name` = '" . $elementSetName . "'";
        return $db->query($sql)->fetchAll();
    }
    
    private function _dropTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        DROP TABLE IF EXISTS `{$db->prefix}files_elements`;
        DROP TABLE IF EXISTS `{$db->prefix}file_element_set_lookup`;";
        $db->execBlock($sql);
    }
    private function _renameTablesRevert()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE 
        `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}files`, 
        `{$db->prefix}files_images" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}files_images`, 
        `{$db->prefix}files_videos" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}files_videos`, 
        `{$db->prefix}file_meta_lookup" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}file_meta_lookup`;";
        $db->exec($sql);
    }
}
