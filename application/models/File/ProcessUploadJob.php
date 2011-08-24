<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for jobs.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class File_ProcessUploadJob extends Omeka_JobAbstract
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
        $file = $this->_db->getTable('File')->find($this->_options['fileId']);
        if (!$file) {
            throw new RuntimeException("File with ID={$this->_options['fileId']} does not exist");
        }
        return $file;
    }
}
