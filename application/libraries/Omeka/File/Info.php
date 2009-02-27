<?php 
/**
* A wrapper for the getID3 library that extracts metadata from files in the 
* Omeka filesystem.
*/
class Omeka_File_Info
{
    /**
     * Take a set of Element records and populate them with element text that is 
     * auto-generated based on the getID3 metadata extraction library.
     * 
     * @param array Set of Element records.
     * @param array Info extracted from the file by the getID3 library.
     * @param string Either 'FilesVideos' or 'FilesImages' depending.
     * @return void
     **/
    protected function populateMimeTypeElements($elements, $id3Info, $extractionStrategy)
    {
        $helperClass = new $extractionStrategy;
        
        $helperClass->initialize($id3Info, $this->getPath('archive'));
        
        // Loop through the elements provided and extract the auto-generated text
        // for each of them.
        foreach ($elements as $element) {
            // Method that is named the same as the element, which is how the data 
            // gets retrieved. E.g. FilesVideos::getBitrate() for the Bitrate element. 
            
            // Strip out whitespace and prepend 'get' to adhere to naming conventions.
            $helperFunction = 'get' . preg_replace('/\s*/', '', $element->name);
            
            if (!method_exists($helperClass, $helperFunction)) {
                throw new Exception("Cannot retrieve metadata for the element called '$element->name'!");
            }
            $elementText = $helperClass->$helperFunction();
            
            // Don't bother saving element texts with null values.
            if ($elementText) {
                $this->addTextForElement($element, $elementText);
            }
        }        
    }
    
    /**
     * Process the extended set of metadata for a file (contingent on its MIME type).
     *
     * @return void
     **/
    public function extractMimeMetadata($path)
    {
        if (!is_readable($path)) {
            throw new Exception( 'File cannot be read!' );
        }
                
        // If we can use the browser mime_type instead of the ID3 extrapolation, 
        // do that
        $mime_type = $this->getMimeType();    
        
        // Return if getid3 did not return a valid object.
        if (!$id3 = $this->retrieveID3Info($path)) {
            return;
        }
        
        if ($this->mimeTypeIsAmbiguous($mime_type)) {
            // If we can't determine MIME type via the browser, we will use the 
            // ID3 data, but be warned that this may cause a memory error on 
            // large files
            $mime_type = $id3->info['mime_type'];
        }
        
        if (!$mime_type) {
            return false;
        } else {
            $this->setMimeType($mime_type);
        }
        
        $elements = $this->getMimeTypeElements($mime_type);

        if (empty($elements)) {
            return;
        }
                
        // Figure out what kind of extraction strategy to use for retrieving the 
        // metadata from ID3. Current possibilities include either FilesImages 
        // or FilesVideos
        switch (current($elements)->set_name) {
            case 'Omeka Video File':
                $extraction = 'FilesVideos';
                break;
            case 'Omeka Image File':
                $extraction = 'FilesImages';
                break;
            default:
                throw new Exception('Cannot extract metadata for these elements!');
                break;
        }
                
        $this->populateMimeTypeElements($elements, $id3->info, $extraction);        
    }
    
    /**
     * References a list of ambiguous mime types from "http://msdn2.microsoft.com/en-us/library/ms775147.aspx".
     * 
     * @param string
     * @return boolean
     **/
    protected function mimeTypeIsAmbiguous($mime_type)
    {
        return in_array($mime_type, array("text/plain", "application/octet-stream", '', null));
    }
    
    /**
     * Pull down the file's extra metadata via getID3 library.
     *
     * @return getID3
     **/
    private function retrieveID3Info($path)
    {
        // Do not extract metadata if the exif module is not loaded. This 
        // applies to all files, not just files with Exif data -- i.e. images.
        if (!extension_loaded('exif')) {
            return false;
        }
        
        require_once 'getid3/getid3.php';
        $id3 = new getID3;
        $id3->encoding = 'UTF-8';
        
        try {
            $id3->Analyze($path);
            return $id3;
        } catch (Exception $e) {
            return false;
        }        
    }
}
