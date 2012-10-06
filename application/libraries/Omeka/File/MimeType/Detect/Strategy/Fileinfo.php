<?php
class Omeka_File_MimeType_Detect_Strategy_Fileinfo 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (!extension_loaded('fileinfo')) {
            // The Fileinfo extension is not installed.
            return false;
        }
        // FILEINFO_MIME_TYPE was introduced in PHP 5.3.0.
        $option = defined(FILEINFO_MIME_TYPE) ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
        $finfo = new finfo($option);
        return $finfo->file($file);
    }
}
