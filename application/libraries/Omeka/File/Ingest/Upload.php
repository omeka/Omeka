<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * This class creates a bridge between the ZF File Transfer HTTP adapter and
 * Omeka's file ingest classes.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_File_Ingest_Upload extends Omeka_File_Ingest_Abstract
{
    /**
     * @var Zend_File_Transfer_Adapter_Http
     **/
    protected $_adapter;
    
    /**
     * @var array Set of options for the $_adapter instance.
     */
    protected $_adapterOptions = array();
            
    protected function _buildAdapter()
    {
        $this->_adapter = new Zend_File_Transfer_Adapter_Http($this->_adapterOptions);
        $this->_adapter->setDestination(self::$_archiveDirectory);
        
        // Add a filter to rename the file to something archive-friendly.
        $this->_adapter->addFilter(new Omeka_Filter_Filename);
    }
    
    /**
     * In addition to the default options available for Omeka_File_Ingest_Abstract,
     * this understands the following options:
     * 
     * 'ignoreNoFile' => boolean False by default.  Whether or not to ignore 
     * validation errors that occur when an uploaded file is missing.  This 
     * may occur when a file input is left empty on a form.  
     * 
     * This option can be overridden by the 'ignore_invalid_files' option.  For 
     * instance, if 'ignoreNoFile' is set to false but 'ignore_invalid_files' is
     * set to true, any exceptions due to missing uploads will be suppressed and
     * ignored.
     * 
     * @param array
     * @return void
     **/
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (array_key_exists('ignoreNoFile', $options)) {
            $this->_adapterOptions['ignoreNoFile'] = $options['ignoreNoFile'];
        }
    }
    
    /**
     * The 'name' attribute of the $_FILES array will always contain the 
     * original name of the file.
     * 
     * @param array $fileInfo
     * @return string
     **/
    protected function _getOriginalFilename($fileInfo)
    {
        return $fileInfo['name'];
    }
    
    /**
     * Use the Zend_File_Transfer adapter to upload the file.  
     * 
     * @internal The resulting filename is retrieved via the adapter's 
     * getFileName() method.
     * 
     * @param array 
     * @param string 
     * @return string
     **/
    protected function _transferFile($fileInfo, $originalFilename)
    {
        // Upload a single file at a time.
        if (!$this->_adapter->receive($fileInfo['form_index'])) {
            throw new Omeka_File_Ingest_InvalidException(join("\n\n", $this->_adapter->getMessages()));
        }

        // Return the path to the file as it is listed in the archive.
        return $this->_adapter->getFileName($fileInfo['form_index']);
    }
        
    /**
     * Use the adapter to extract the array of file information.
     * 
     * @param string|null $fileInfo The name of the form input to ingest.
     * @return void
     **/
    protected function _parseFileInfo($fileInfo)
    {
        if (!$this->_adapter) {
            $this->_buildAdapter();
        }
                        
        // Grab the info from $_FILES array (prior to receiving the files).
        $fileInfoArray = $this->_adapter->getFileInfo($fileInfo);
       
        // Include the index of the form so that we can use that if necessary.
        foreach ($fileInfoArray as $index => $info) {
            // We need the index of this as well b/c the file info is passed 
            // around (not the form index).
            $info['form_index'] = $index;
            $fileInfoArray[$index] = $info;
        }
        return $fileInfoArray;
    }
    
    /**
     * Use the Zend Framework adapter to handle validation instead of the 
     * built-in _validateFile() method.
     * 
     * @see Omeka_File_Ingest_Abstract::_validateFile()
     * @param Zend_Validate_Interface
     * @return $this
     **/
    public function addValidator(Zend_Validate_Interface $validator)
    {
        if (!$this->_adapter) {
            $this->_buildAdapter();
        }

        $this->_adapter->addValidator($validator);
    }
}
