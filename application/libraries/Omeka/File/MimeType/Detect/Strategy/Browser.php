<?php
class Omeka_File_MimeType_Detect_Strategy_Browser 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        if (empty($_FILES)) {
            // No files have been uploaded.
            return false;
        }
        // If an uploaded file's tmp_name matches the passed file, return the 
        // MIME type set by the browser.
        foreach ($_FILES as $_file) {
            if ($file == $_file['tmp_name']) {
                return $_file['type'];
            }
        }
        // No uploaded files match the passed file.
        return false;
    }
}
