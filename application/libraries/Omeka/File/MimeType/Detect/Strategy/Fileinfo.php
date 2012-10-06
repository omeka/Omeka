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
        $finfo = new finfo(FILEINFO_MIME);
        return $finfo->file($file);
    }
}
