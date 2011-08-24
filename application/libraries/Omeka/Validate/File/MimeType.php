<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Validates files against a MIME type whitelist.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Validate_File_MimeType extends Zend_Validate_File_MimeType
{
    const DEFAULT_WHITELIST = 'application/msword,application/ogg,application/pdf,application/rtf,application/vnd.ms-access,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.ms-project,application/vnd.ms-write,application/vnd.oasis.opendocument.chart,application/vnd.oasis.opendocument.database,application/vnd.oasis.opendocument.formula,application/vnd.oasis.opendocument.graphics,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/x-ms-wmp,application/x-ogg,application/x-gzip,application/x-msdownload,application/x-shockwave-flash,application/x-tar,application/zip,audio/aac,audio/aiff,audio/mid,audio/midi,audio/mp3,audio/mp4,audio/mpeg,audio/mpeg3,audio/ogg,audio/wav,audio/wma,audio/x-aac,audio/x-aiff,audio/x-midi,audio/x-mp3,audio/x-mp4,audio/x-mpeg,audio/x-mpeg3,audio/x-mpegaudio,audio/x-ms-wax,audio/x-realaudio,audio/x-wav,audio/x-wma,image/bmp,image/gif,image/icon,image/jpeg,image/pjpeg,image/png,image/tiff,image/x-icon,image/x-ms-bmp,text/css,text/plain,text/richtext,text/rtf,video/asf,video/avi,video/divx,video/mp4,video/mpeg,video/msvideo,video/ogg,video/quicktime,video/x-ms-wmv,video/x-msvideo';
    const WHITELIST_OPTION = 'file_mime_type_whitelist';
    
    /**
     * Omeka-specific error messages for validating MIME types.
     * Also fixes some grammatical errors in the original validator error 
     * messages.
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::FALSE_TYPE   => "The file '%value%' could not be ingested because it has a disallowed MIME type (%type%).",
        self::NOT_DETECTED => "The file '%value%' could not be ingested because its media (MIME) type could not be detected.",
        self::NOT_READABLE => "The file '%value%' could not be ingested because it is not readable."
    );
    
    /**
     * Constructor.
     * 
     * Use the 'file_mime_type_whitelist' option if nothing is passed in as the
     * default.
     * 
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (!$options) {
            $options = (string)get_option(self::WHITELIST_OPTION);
        }
                
        parent::__construct($options);
    }
}
