<?php
interface Omeka_File_Transfer_Adapter_Interface
{
    public function setFileInfo(array $fileInfo);
    
    public function getOriginalFileName();
    
    public function transferFile($destination);
    
    public function isValid();
}