<?php
class Omeka_File_MimeType_Detect_Strategy_FileCommand 
    implements Omeka_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        $disabled = explode(', ', ini_get('disable_functions'));
        if (in_array('shell_exec', $disabled)) {
            // shell_exec is disabled.
            return false;
        }
        $fileArg = escapeshellarg($file);
        $command = "file -ib $fileArg";
        return trim(shell_exec($command));
    }
}
