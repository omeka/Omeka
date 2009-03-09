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
    protected function _getRecordMetadata($record, $fieldName)
    {
        switch (strtolower($fieldName)) {
            case 'id':
                return $record->id;
                break;
            case 'archive filename':
                return $record->archive_filename;
                break;
            case 'original filename':
                return $record->original_filename;
                break;
            case 'size':
                return $record->size;
                break;
            case 'mime type':
                return $record->getMimeType();
                break;
            case 'date added':
                return $record->added;
                break;
            case 'date modified':
                return $record->modified;
                break;
            case 'authentication':
                return $record->authentication;
                break;
            // 'MIME Type OS' and 'File Type OS' to be deprecated?
            case 'mime type os':
                return $record->mime_os;
                break;
            case 'file type os':
                return $record->type_os;
                break;
            case 'uri':
                return $this->_getUri($record, 'archive');
                break;
            case 'fullsize uri':
                return $this->_getUri($record, 'fullsize');
                break;
            case 'thumbnail uri':
                return $this->_getUri($record, 'thumbnail');
                break;
            case 'square thumbnail uri':
                return $this->_getUri($record, 'square_thumbnail');
                break;
            case 'permalink':
                return abs_uri(array('controller'=>'files', 'action'=>'show', 'id'=>$record->id));
                break;
            default:
                throw new Exception("'$specialValue' is an invalid special value.");
                break;
        }
    }
    
    protected function _getUri($file, $sizeFormat)
    {
        $options = array('id'=>$file->id, 'format'=>$sizeFormat);
        return abs_uri($options, 'display');
    }
}
