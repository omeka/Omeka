<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Retrieve metadata for files.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_View_Helper_FileMetadata extends Omeka_View_Helper_RecordMetadata
{
    /**
     * Returns the metadata of a file.
     * You can use this function in the view by calling $this->fileMetadata(...)
     *
     * @param File $file
     * @param string $elementSetName The element set name for the item metadata. Example: 'Dublin Core'
     * For internal file metadata,the $elementSetName is the same as the $fieldName in _getRecordMetadata. Example: 'archive filename'
     * @param string $elementName The element name for the item metadata. Example: 'Title'
     * For internal file metadata, you do not supply an $elementName
     * @param array $options
     * @return mixed
     */
    public function fileMetadata(File $file,
                         $elementSetName,
                         $elementName = null,
                         $options     = array())
    {
        return $this->_get($file, $elementSetName, $elementName, $options);
    }

    /**
     * @todo What other file metadata should be retrievable?  Presumably we
     * shouldn't display the physical path to the filename, but maybe the web
     * path?  Do we want to display all the various MIME types we pulled in, or
     * just 'MIME type' and plan to make a definitive one of those?
     *
     * @param string
     * @return mixed
     */
    protected function _getRecordMetadata($record, $fieldName)
    {
        switch (strtolower($fieldName)) {
            case 'id':
                return $record->id;
            case 'archive filename':
                return $record->archive_filename;
            case 'original filename':
                return $record->original_filename;
            case 'size':
                return $record->size;
            case 'mime type':
                return $record->getMimeType();
            case 'date added':
                return $record->added;
            case 'date modified':
                return $record->modified;
            case 'authentication':
                return $record->authentication;
            // 'MIME Type OS' and 'File Type OS' to be deprecated?
            case 'mime type os':
                return $record->mime_os;
            case 'file type os':
                return $record->type_os;
            case 'uri':
                return $this->_getUri($record, 'archive');
            case 'fullsize uri':
                return $this->_getUri($record, 'fullsize');
            case 'thumbnail uri':
                return $this->_getUri($record, 'thumbnail');
            case 'square thumbnail uri':
                return $this->_getUri($record, 'square_thumbnail');
            case 'permalink':
                return abs_uri(array('controller'=>'files', 'action'=>'show', 'id'=>$record->id));
            default:
                throw new Exception(__("%s is an invalid special value.", $specialValue));
        }
    }

    protected function _getUri($file, $sizeFormat)
    {
        return $file->getWebPath($sizeFormat);
    }
}
