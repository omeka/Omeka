<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Validate_File_MimeType extends Zend_Validate_File_MimeType
{
    const DEFAULT_WHITELIST = 'application/msword,application/pdf,application/rtf,application/vnd.ms-access,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.ms-project,application/vnd.ms-write,application/vnd.oasis.opendocument.chart,application/vnd.oasis.opendocument.database,application/vnd.oasis.opendocument.formula,application/vnd.oasis.opendocument.graphics,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/x-gzip,application/x-msdownload,application/x-shockwave-flash,application/x-tar,application/zip,audio/midi,audio/mpeg,audio/ogg,audio/wav,audio/wma,audio/x-realaudio,image/bmp,image/gif,image/jp2,image/jpeg,image/png,image/tiff,image/x-icon,text/css,text/plain,text/richtext,video/asf,video/avi,video/divx,video/mpeg,video/quicktime';

    /**
     * @var array Omeka-specific error messages for validating MIME types.  Also
     * fixes some grammatical errors in the original validator error messages.
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
     * @return void
     **/
    public function __construct($options = null)
    {
        if (!$options) {
            $options = get_option('file_mime_type_whitelist');
        }
        parent::__construct($options);
    }
}