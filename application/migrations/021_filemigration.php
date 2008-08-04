<?php
class FileMigration extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__21';
    
    // Record type names
    const RECORD_TYPE_FILE = 'File';
    
    // Element set names. These may need to be changed if previous migrations 
    // change.
    const ELEMENT_SET_DUBLIN_CORE       = 'Dublin Core';
    const ELEMENT_SET_OMEKA_LEGACY_FILE = 'Omeka Legacy File';
    const ELEMENT_SET_OMEKA_IMAGE_FILE  = 'Omeka Image File';
    const ELEMENT_SET_OMEKA_VIDEO_FILE  = 'Omeka Video File';
    
    // Data type names. These may need to be changed if previous migrations 
    // change.
    const DATA_TYPE_TEXT      = 'Text';
    const DATA_TYPE_TINYTEXT  = 'Tiny Text';
    const DATA_TYPE_DATERANGE = 'Date Range';
    const DATA_TYPE_DATE      = 'Date';
    const DATA_TYPE_INTEGER   = 'Integer';
    const DATA_TYPE_BOOLEAN   = 'Boolean';
    const DATA_TYPE_DATETIME  = 'Date Time';
    
    // Element names. Remember that the unique index for the `elements` table is 
    // a `name`/`element_set_id` pair. This distinction is not made here because 
    // up to this point identical element names will stay identical (e.g. 
    // 'Width' for ELEMENT_SET_OMEKA_IMAGE_FILE and ELEMENT_SET_OMEKA_VIDEO_FILE 
    // element sets). Any distinction is made programatically.
    
    // elements from ELEMENT_SET_DUBLIN_CORE
    const ELEMENT_CONTRIBUTOR = 'Contributor';
    const ELEMENT_COVERAGE    = 'Coverage';
    const ELEMENT_CREATOR     = 'Creator';
    const ELEMENT_DATE        = 'Date';
    const ELEMENT_DESCRIPTION = 'Description';
    const ELEMENT_FORMAT      = 'Format';
    const ELEMENT_IDENTIFIER  = 'Identifier';
    const ELEMENT_LANGUAGE    = 'Language';
    const ELEMENT_PUBLISHER   = 'Publisher';
    const ELEMENT_RELATION    = 'Relation';
    const ELEMENT_RIGHTS      = 'Rights';
    const ELEMENT_SOURCE      = 'Source';
    const ELEMENT_SUBJECT     = 'Subject';
    const ELEMENT_TITLE       = 'Title';
    const ELEMENT_TYPE        = 'Type';
    // elements from ELEMENT_SET_OMEKA_LEGACY_FILE
    const ELEMENT_ADDITIONAL_CREATOR = 'Additional Creator';
    const ELEMENT_TRANSCRIBER        = 'Transcriber';
    const ELEMENT_PRODUCER           = 'Producer';
    const ELEMENT_RENDER_DEVICE      = 'Render Device';
    const ELEMENT_RENDER_DETAILS     = 'Render Details';
    const ELEMENT_CAPTURE_DATE       = 'Capture Date';
    const ELEMENT_CAPTURE_DEVICE     = 'Capture Device';
    const ELEMENT_CAPTURE_DETAILS    = 'Capture Details';
    const ELEMENT_CHANGE_HISTORY     = 'Change History';
    const ELEMENT_WATERMARK          = 'Watermark';
    const ELEMENT_AUTHENTICATION     = 'Authentication';
    const ELEMENT_ENCRYPTION         = 'Encryption';
    const ELEMENT_COMPRESSION        = 'Compression';
    const ELEMENT_POST_PROCESSING    = 'Post Processing';
    // elements from ELEMENT_SET_OMEKA_IMAGE_FILE and ELEMENT_SET_OMEKA_VIDEO_FILE
    const ELEMENT_WIDTH  = 'Width';
    const ELEMENT_HEIGHT = 'Height';
    // elements from ELEMENT_SET_OMEKA_IMAGE_FILE
    const ELEMENT_BIT_DEPTH   = 'Bit Depth';
    const ELEMENT_CHANNELS    = 'Channels';
    const ELEMENT_EXIF_STRING = 'Exif String';
    const ELEMENT_EXIF_ARRAY  = 'Exif Array';
    const ELEMENT_IPTC_STRING = 'IPTC String';
    const ELEMENT_IPTC_ARRAY  = 'IPTC Array';
    // elements from ELEMENT_SET_OMEKA_VIDEO_FILE
    const ELEMENT_BITRATE     = 'Bitrate';
    const ELEMENT_DURATION    = 'Duration';
    const ELEMENT_SAMPLE_RATE = 'Sample Rate';
    const ELEMENT_CODEC       = 'Codec';
    
    // Helper mapping array for ELEMENT_SET_DUBLIN_CORE elements
    private $_eMapDc = array('title'      => self::ELEMENT_TITLE, 
                             'publisher'  => self::ELEMENT_PUBLISHER, 
                             'language'   => self::ELEMENT_LANGUAGE, 
                             'relation'   => self::ELEMENT_RELATION, 
                             'coverage'   => self::ELEMENT_COVERAGE, 
                             'rights'     => self::ELEMENT_RIGHTS, 
                             'description'=> self::ELEMENT_DESCRIPTION, 
                             'source'     => self::ELEMENT_SOURCE, 
                             'subject'    => self::ELEMENT_SUBJECT, 
                             'creator'    => self::ELEMENT_CREATOR, 
                             'date'       => self::ELEMENT_DATE, 
                             'format'     => self::ELEMENT_FORMAT);
    // Helper mapping array for the ELEMENT_SET_OMEKA_LEGACY_FILE elements
    private $_eMapLegacy = array('additional_creator' => self::ELEMENT_ADDITIONAL_CREATOR, 
                                 'transcriber'        => self::ELEMENT_TRANSCRIBER, 
                                 'producer'           => self::ELEMENT_PRODUCER, 
                                 'render_device'      => self::ELEMENT_RENDER_DEVICE, 
                                 'render_details'     => self::ELEMENT_RENDER_DETAILS, 
                                 'capture_date'       => self::ELEMENT_CAPTURE_DATE, 
                                 'capture_device'     => self::ELEMENT_CAPTURE_DEVICE, 
                                 'capture_details'    => self::ELEMENT_CAPTURE_DETAILS, 
                                 'change_history'     => self::ELEMENT_CHANGE_HISTORY, 
                                 'watermark'          => self::ELEMENT_WATERMARK, 
                                 'authentication'     => self::ELEMENT_AUTHENTICATION, 
                                 'encryption'         => self::ELEMENT_ENCRYPTION, 
                                 'compression'        => self::ELEMENT_COMPRESSION, 
                                 'post_processing'    => self::ELEMENT_POST_PROCESSING);
    // Helper mapping array for ELEMENT_SET_OMEKA_IMAGE_FILE elements
    private $_eMapImage = array('width'       => self::ELEMENT_WIDTH, 
                                'height'      => self::ELEMENT_HEIGHT, 
                                'bit_depth'   => self::ELEMENT_BIT_DEPTH, 
                                'channels'    => self::ELEMENT_CHANNELS, 
                                'exif_string' => self::ELEMENT_EXIF_STRING, 
                                'exif_array'  => self::ELEMENT_EXIF_ARRAY, 
                                'iptc_string' => self::ELEMENT_IPTC_STRING, 
                                'iptc_array'  => self::ELEMENT_IPTC_ARRAY);
    // Helper mapping array for ELEMENT_SET_OMEKA_VIDEO_FILE elements
    private $_eMapVideo = array('bitrate'     => self::ELEMENT_BITRATE, 
                                'duration'    => self::ELEMENT_DURATION, 
                                'sample_rate' => self::ELEMENT_SAMPLE_RATE, 
                                'codec'       => self::ELEMENT_CODEC, 
                                'width'       => self::ELEMENT_WIDTH, 
                                'height'      => self::ELEMENT_HEIGHT);
    
    // New record types
    private $_rt = array(
        array('name' => self::RECORD_TYPE_FILE, 
              'description' => 'Elements, element sets, and element texts assigned to this record type relate to file records.'),
    );
    
    // New element sets
    private $_es = array(
        array('name' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'description' => 'The metadata element set that, in addition to the Dublin Core element set, was included in the `files` table in previous versions of Omeka. These elements are common to all Omeka files. This set may be deprecated in future versions.'), 
        array('name' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'description' => 'The metadata element set that was included in the `files_images` table in previous versions of Omeka. These elements are common to all image files.'), 
        array('name' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'description' => 'The metadata element set that was included in the `files_videos` table in previous versions of Omeka. These elements are common to all video files.'), 
    );
    
    // New elements.
    private $_e = array(
        // Omeka Legacy File Elements
        array('name' => self::ELEMENT_ADDITIONAL_CREATOR, 
              'order' => 1,
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_TRANSCRIBER, 
              'order' => 2, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_PRODUCER, 
              'order' => 3, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_RENDER_DEVICE, 
              'order' => 4, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_RENDER_DETAILS, 
              'order' => 5, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_CAPTURE_DATE, 
              'order' => 6, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_DATETIME, 
              'description' => ''), 
        array('name' => self::ELEMENT_CAPTURE_DEVICE, 
              'order' => 7, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_CAPTURE_DETAILS, 
              'order' => 8, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_CHANGE_HISTORY, 
              'order' => 9, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_WATERMARK, 
              'order' => 10, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_AUTHENTICATION, 
              'order' => 11, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_ENCRYPTION, 
              'order' => 12, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_COMPRESSION, 
              'order' => 13, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_POST_PROCESSING, 
              'order' => 14, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_LEGACY_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        // Omeka Image File Elements
        array('name' => self::ELEMENT_WIDTH, 
              'order' => 1, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_HEIGHT, 
              'order' => 2, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_BIT_DEPTH, 
              'order' => 3, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_CHANNELS, 
              'order' => 4, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_EXIF_STRING, 
              'order' => 5, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_EXIF_ARRAY, 
              'order' => 6, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_IPTC_STRING, 
              'order' => 7, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_IPTC_ARRAY, 
              'order' => 8, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_IMAGE_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        // Omeka Video File Elements
        array('name' => self::ELEMENT_BITRATE, 
              'order' => 1, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_DURATION, 
              'order' => 2, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_SAMPLE_RATE, 
              'order' => 3, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_CODEC, 
              'order' => 4, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_TEXT, 
              'description' => ''), 
        array('name' => self::ELEMENT_WIDTH, 
              'order' => 5, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
        array('name' => self::ELEMENT_HEIGHT, 
              'order' => 6, 
              'recordTypeName' => self::RECORD_TYPE_FILE, 
              'elementSetName' => self::ELEMENT_SET_OMEKA_VIDEO_FILE, 
              'dataTypeName' => self::DATA_TYPE_INTEGER, 
              'description' => ''), 
    );
    
    // The method call order is very important here.
    public function up()
    {
        // Backup tables and create the new ones.
        $this->_renameTablesBackup();
        $this->_createNewTables();
        
        // record_types
        $this->_insertRecordTypes();
        
        // element_sets
        $this->_insertElementSets();
        $this->_updateElementSets();
        
        // mime_element_set_lookup
        $this->_insertMimeElementSetLookup();
        
        // elements
        $this->_insertElements();
        
        // element_texts
        $this->_insertElementTexts();
        
        // files
        $this->_insertFiles();
    }
    
    public function down()
    {
        // Revert to the pre-migration state.
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
          `size` int(10) unsigned NOT NULL,
          `has_derivative_image` tinyint(1) NOT NULL,
          `mime_browser` varchar(255) collate utf8_unicode_ci default NULL,
          `mime_os` varchar(255) collate utf8_unicode_ci default NULL,
          `type_os` varchar(255) collate utf8_unicode_ci default NULL,
          `archive_filename` text collate utf8_unicode_ci NOT NULL,
          `original_filename` text collate utf8_unicode_ci NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `item_id` (`item_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        
        DROP TABLE IF EXISTS `{$db->prefix}mime_element_set_lookup`;
        CREATE TABLE `{$db->prefix}mime_element_set_lookup` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `element_set_id` int(10) unsigned NOT NULL,
          `mime` varchar(255) collate utf8_unicode_ci NOT NULL,
          PRIMARY KEY  (`id`),
          UNIQUE KEY `mime` (`mime`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->execBlock($sql);
    }
    
    private function _dropTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        
        DROP TABLE IF EXISTS `{$db->prefix}mime_element_set_lookup`;";
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
    
    /**************************************************************************/
    // record_types
    
    private function _insertRecordTypes()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}record_types` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_rt as $recordType) {
            $name        = $recordType['name'];
            $description = $recordType['description'];
            $db->exec($sql, array($name, $description));
        }
    }
    
    /**************************************************************************/
    // element_sets
    
    private function _insertElementSets()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}element_sets` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_es as $elementSet) {
            $name        = $elementSet['name'];
            $description = $elementSet['description'];
            $db->exec($sql, array($name, $description));
        }
    }
    
    private function _updateElementSets()
    {
        $db = $this->db;
        $sql = "
        -- Set the 'File' record type ID for element sets in the 'Omeka Legacy 
        -- File', 'Omeka Image File' and 'Omeka Video File' element sets.
        UPDATE `{$db->prefix}element_sets` es 
        JOIN `{$db->prefix}record_types` rt 
        SET es.`record_type_id` = rt.`id` 
        WHERE (
            es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_FILE . "' 
            OR es.`name` = '" . self::ELEMENT_SET_OMEKA_IMAGE_FILE . "' 
            OR es.`name` = '" . self::ELEMENT_SET_OMEKA_VIDEO_FILE . "'
        ) 
        AND rt.`name` = '" . self::RECORD_TYPE_FILE . "';";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // mime_element_set_lookup
    
    private function _insertMimeElementSetLookup()
    {
        $db = $this->db;
        $sql = "
        INSERT `{$db->prefix}mime_element_set_lookup` (
            `element_set_id`, 
            `mime`
        )
        SELECT 
            CASE `table_class` 
                WHEN 'FilesImages' THEN (
                    SELECT `id` FROM `{$db->prefix}element_sets` 
                    WHERE `name` = '" . self::ELEMENT_SET_OMEKA_IMAGE_FILE . "'
                ) 
                WHEN 'FilesVideos' THEN (
                    SELECT `id` FROM `{$db->prefix}element_sets` 
                    WHERE `name` = '" . self::ELEMENT_SET_OMEKA_VIDEO_FILE . "'
                ) 
            END, 
            `mime_type` 
        FROM `{$db->prefix}file_meta_lookup" . self::BACKUP_TABLE_SUFFIX . "`";
        $db->exec($sql);
    }
    
    /**************************************************************************/
    // elements
    
    private function _insertElements()
    {
        $db = $this->db;
        $sql = "
        INSERT `{$db->prefix}elements` (
            `record_type_id`, 
            `data_type_id`, 
            `element_set_id`, 
            `name`, 
            `description`, 
            `order`
        ) VALUES ( 
            (SELECT `id` FROM `{$db->prefix}record_types` WHERE `name` = ?), 
            (SELECT `id` FROM `{$db->prefix}data_types` WHERE `name` = ?), 
            (SELECT `id` FROM `{$db->prefix}element_sets` WHERE `name` = ?), 
            ?, 
            ?, 
            ? 
        )";
        foreach ($this->_e as $element) {
            $db->exec($sql, array($element['recordTypeName'], 
                                  $element['dataTypeName'], 
                                  $element['elementSetName'], 
                                  $element['name'], 
                                  $element['description'], 
                                  $element['order']));
        }
    }
    
    /**************************************************************************/
    // element_texts
    
    private function _insertElementTexts()
    {
        $db = $this->db;
        
        $sql = "
        SELECT `id` 
        FROM `{$db->prefix}record_types` 
        WHERE `name` = '" . self::RECORD_TYPE_FILE . "'";
        $fileRecordType = $db->fetchRow($sql);
        
        // Migrate the ELEMENT_SET_DUBLIN_CORE element texts
        $this->_insertElementTextsDc($fileRecordType['id']);
        
        // Migrate the ELEMENT_SET_OMEKA_LEGACY_FILE element texts
        $this->_insertElementTextsLegacy($fileRecordType['id']);
        
        // Migrate the ELEMENT_SET_OMEKA_IMAGE_FILE element texts
        $this->_insertElementTextsImage($fileRecordType['id']);
        
        // Migrate the ELEMENT_SET_OMEKA_VIDEO_FILE element texts
        $this->_insertElementTextsVideo($fileRecordType['id']);
    }
    
    private function _insertElementTextsDc($fileRecordTypeId)
    {
        $db = $this->db;
        foreach ($this->_eMapDc as $originalElementName => $elementName) {
            $sql = "
            INSERT `{$db->prefix}element_texts` (
                `record_id`, 
                `record_type_id`, 
                `element_id`, 
                `html`, 
                `text`
            ) 
            SELECT 
                `id`, 
                $fileRecordTypeId, 
                -- remember that the unique index is the `name` and 
                -- `element_set_id` pair
                (
                    SELECT e.`id` 
                    FROM `{$db->prefix}elements` e 
                    JOIN `{$db->prefix}element_sets` es 
                    WHERE e.`element_set_id` = es.`id` 
                    AND e.`name` = '$elementName' 
                    AND es.`name` = '" . self::ELEMENT_SET_DUBLIN_CORE . "'
                ), 
                0, 
                `$originalElementName` 
            FROM `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` 
            WHERE `$originalElementName` IS NOT NULL 
            AND `$originalElementName` != ''";
            $db->exec($sql);
        }
    }
    
    private function _insertElementTextsLegacy($fileRecordTypeId)
    {
        $db = $this->db;
        foreach ($this->_eMapLegacy as $originalElementName => $elementName) {
            $sql = "
            INSERT `{$db->prefix}element_texts` (
                `record_id`, 
                `record_type_id`, 
                `element_id`, 
                `html`, 
                `text`
            ) 
            SELECT 
                `id`, 
                $fileRecordTypeId, 
                -- remember that the unique index is the `name` and 
                -- `element_set_id` pair
                (
                    SELECT e.`id` 
                    FROM `{$db->prefix}elements` e 
                    JOIN `{$db->prefix}element_sets` es 
                    WHERE e.`element_set_id` = es.`id` 
                    AND e.`name` = '$elementName' 
                    AND es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_FILE . "'
                ), 
                0, 
                `$originalElementName` 
            FROM `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` 
            WHERE `$originalElementName` IS NOT NULL 
            AND `$originalElementName` != ''";
            $db->exec($sql);
        }
    }
    
    private function _insertElementTextsImage($fileRecordTypeId)
    {
        $db = $this->db;
        foreach ($this->_eMapImage as $originalElementName => $elementName) {
            $sql = "
            INSERT `{$db->prefix}element_texts` (
                `record_id`, 
                `record_type_id`, 
                `element_id`, 
                `html`, 
                `text`
            ) 
            SELECT 
                `file_id`, 
                $fileRecordTypeId, 
                -- remember that the unique index is the `name` and 
                -- `element_set_id` pair
                (
                    SELECT e.`id` 
                    FROM `{$db->prefix}elements` e 
                    JOIN `{$db->prefix}element_sets` es 
                    WHERE e.`element_set_id` = es.`id` 
                    AND e.`name` = '$elementName' 
                    AND es.`name` = '" . self::ELEMENT_SET_OMEKA_IMAGE_FILE . "'
                ), 
                0, 
                `$originalElementName` 
            FROM `{$db->prefix}files_images" . self::BACKUP_TABLE_SUFFIX . "` 
            WHERE `$originalElementName` IS NOT NULL 
            AND `$originalElementName` != ''";
            $db->exec($sql);
        }
    }
    
    private function _insertElementTextsVideo($fileRecordTypeId)
    {
        $db = $this->db;
        foreach ($this->_eMapVideo as $originalElementName => $elementName) {
            $sql = "
            INSERT `{$db->prefix}element_texts` (
                `record_id`, 
                `record_type_id`, 
                `element_id`, 
                `html`, 
                `text`
            ) 
            SELECT 
                `file_id`, 
                $fileRecordTypeId, 
                -- remember that the unique index is the `name` and 
                -- `element_set_id` pair
                (
                    SELECT e.`id` 
                    FROM `{$db->prefix}elements` e 
                    JOIN `{$db->prefix}element_sets` es 
                    WHERE e.`element_set_id` = es.`id` 
                    AND e.`name` = '$elementName' 
                    AND es.`name` = '" . self::ELEMENT_SET_OMEKA_VIDEO_FILE . "'
                ), 
                0, 
                `$originalElementName` 
            FROM `{$db->prefix}files_videos" . self::BACKUP_TABLE_SUFFIX . "` 
            WHERE `$originalElementName` IS NOT NULL 
            AND `$originalElementName` != ''";
            $db->exec($sql);
        }
    }
    
    /**************************************************************************/
    // files
    
    private function _insertFiles()
    {
        $db = $this->db;
        $sql = "
        INSERT `{$db->prefix}files` (
            `id`, 
            `item_id`, 
            `size`, 
            `has_derivative_image`, 
            `mime_browser`, 
            `mime_os`, 
            `type_os`, 
            `archive_filename`, 
            `original_filename`
        ) 
        SELECT 
            `id`, 
            `item_id`, 
            `size`, 
            `has_derivative_image`, 
            `mime_browser`, 
            `mime_os`, 
            `type_os`, 
            `archive_filename`, 
            `original_filename`
        FROM `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "`";
        $db->exec($sql);
    }
}
