<?php
class Omeka_File_MimeType_Detect_Strategy_MimeContentType 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (!function_exists('mime_content_type')) {
            // The mime_content_type has been deprecated and will be removed in 
            // future versions of PHP.
            return false;
        }
        return mime_content_type($file);
    }
}
