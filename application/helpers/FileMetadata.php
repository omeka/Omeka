<?php 

/**
* 
*/
class Omeka_View_Helper_FileMetadata extends Omeka_View_Helper_RecordMetadata
{
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
     **/
    protected function _getRecordMetadata($fieldName)
    {
        switch (strtolower($fieldName)) {
            case 'id':
                return $this->_record->id;
                break;
            case 'archive filename':
                return $this->_record->archive_filename;
                break;
            case 'original filename':
                return $this->_record->original_filename;
                break;
            case 'size':
                return $this->_record->size;
                break;
            case 'mime type':
                return $this->_record->getMimeType();
                break;
            case 'date added':
                return $this->_record->added;
                break;
            case 'date modified':
                return $this->_record->modified;
                break;
            default:
                throw new Exception("'$specialValue' is an invalid special value.");
                break;
        }
    }
}
