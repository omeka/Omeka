<?php
class Omeka_File_MimeType_Detect_Strategy_GetId3 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (!extension_loaded('exif')) {
            // The getid3 library requires the exif extension.
            return false;
        }
        require_once 'getid3/getid3.php';
        $id3 = new getID3;
        $id3->encoding = 'UTF-8';
        try {
            $id3->Analyze($file);
        } catch (getid3_exception $e) {
            return false;
        }
        if (isset($id3->info['mime_type'])) {
            return $id3->info['mime_type'];
        }
        return false;
    }
}
