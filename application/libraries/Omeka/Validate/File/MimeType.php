<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Validates files against a MIME type whitelist.
 * 
 * @package Omeka\Validate
 */
class Omeka_Validate_File_MimeType extends Zend_Validate_Abstract
{
    const DEFAULT_WHITELIST = 'application/msword,application/ogg,application/pdf,application/rtf,application/vnd.ms-access,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.ms-project,application/vnd.ms-write,application/vnd.oasis.opendocument.chart,application/vnd.oasis.opendocument.database,application/vnd.oasis.opendocument.formula,application/vnd.oasis.opendocument.graphics,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/x-ms-wmp,application/x-ogg,application/x-gzip,application/x-msdownload,application/x-shockwave-flash,application/x-tar,application/zip,audio/aac,audio/aiff,audio/mid,audio/midi,audio/mp3,audio/mp4,audio/mpeg,audio/mpeg3,audio/ogg,audio/wav,audio/wma,audio/x-aac,audio/x-aiff,audio/x-midi,audio/x-mp3,audio/x-mp4,audio/x-mpeg,audio/x-mpeg3,audio/x-mpegaudio,audio/x-ms-wax,audio/x-realaudio,audio/x-wav,audio/x-wma,image/bmp,image/gif,image/icon,image/jpeg,image/pjpeg,image/png,image/tiff,image/x-icon,image/x-ms-bmp,text/css,text/plain,text/richtext,text/rtf,video/asf,video/avi,video/divx,video/mp4,video/mpeg,video/msvideo,video/ogg,video/quicktime,video/x-ms-wmv,video/x-msvideo';
    const WHITELIST_OPTION = 'file_mime_type_whitelist';
    const INVALID_TYPE = 'fileMimeTypeInvalid';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_TYPE => "The file '%file%' could not be ingested because it has a disallowed MIME type (%mimetype%).",
    );
    
    /**
     * @var array
     */
    protected $_messageVariables = array(
        'file' => '_file', 
        'mimetype' => '_mimeType', 
    );
    
    /**
     * @var string
     */
    protected $_customWhitelist;
    
    /**
     * @var string
     */
    protected $_file;
    
    /**
     * @var string
     */
    protected $_mimeType;
    
    /**
     * Construct the validator object.
     */
    public function __construct()
    {
        $this->_customWhitelist = get_option(self::WHITELIST_OPTION);
    }
    
    /**
     * Vaidate the file MIME type.
     * 
     * @param string $file
     * @return bool
     */
    public function isValid($file)
    {
        $this->_file = $file;
        
        // Detect the definitive MIME type.
        $detect = new Omeka_File_MimeType_Detect($this->_file);
        $this->_mimeType = $detect->detect();
        
        // Set the relevant MIME type whitelist.
        if ($this->_customWhitelist) {
            $whitelist = $this->_customWhitelist;
        } else {
            $whitelist = self::DEFAULT_WHITELIST;
        }
        
        // Validate the MIME type against the whitelist.
        if (in_array($this->_mimeType, explode(',', $whitelist))) {
            // Valid MIME type. Set the MIME type to the ingest class so that it 
            // can assign it to the File record. Doing this avoids more than one 
            // call to the MIME type detection class.
            Omeka_File_Ingest_AbstractIngest::$mimeType = $this->_mimeType;
            return true;
        } else {
            // Invalid MIME type.
            $this->_error(self::INVALID_TYPE);
            return false;
        }
    }
}
