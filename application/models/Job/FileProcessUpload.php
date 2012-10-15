<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Job
 */
class Job_FileProcessUpload extends Omeka_Job_AbstractJob
{
    public function perform()
    {
        $file = $this->_getFile();
        // Extract the metadata.  This will have one side effect (aside from
        // adding the new metadata): it uses setMimeType() to reset the default
        // mime type for the file if applicable.
        try {
            $file->extractMetadata();
            $file->createDerivatives();
            $file->storeFiles();
        } catch (Exception $e) {
            $file->delete();
            throw $e;
        }
    }

    private function _getFile()
    {
        $file = new File;
        $file->setArray($this->_options['fileData']);
        return $file;
    }
}
