<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * This class creates a bridge between the ZF File Transfer HTTP adapter and
 * Omeka's file ingest classes.
 * 
 * @package Omeka\File\Ingest
 */
class Omeka_File_Ingest_Upload extends Omeka_File_Ingest_AbstractIngest
{
    /**
     * @var Zend_File_Transfer_Adapter_Http
     */
    protected $_adapter;

    /**
     * @var array Set of options for the {@link $_adapter} instance.
     */
    protected $_adapterOptions = array();

    /**
     * Create a ZF HTTP file transfer adapter.
     */
    protected function _buildAdapter()
    {
        $storage = Zend_Registry::get('storage');

        $this->_adapter = new Zend_File_Transfer_Adapter_Http($this->_adapterOptions);
        $this->_adapter->setDestination($storage->getTempDir());

        // Add a filter to rename the file to something Omeka-friendly.
        $this->_adapter->addFilter(new Omeka_Filter_Filename);
    }

    /**
     * In addition to the default options available for 
     * Omeka_File_Ingest_AbstractIngest, this understands the following options:
     * - 'ignoreNoFile' => boolean False by default.  Whether or not to ignore 
     * - validation errors that occur when an uploaded file is missing.  This 
     * - may occur when a file input is left empty on a form.  
     * 
     * This option can be overridden by the 'ignore_invalid_files' option.  For 
     * instance, if 'ignoreNoFile' is set to false but 'ignore_invalid_files' is
     * set to true, any exceptions due to missing uploads will be suppressed and
     * ignored.
     * 
     * @param array $options
     */
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
     */
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
     * @param array $fileInfo
     * @param string $originalFilename
     * @return string Path to the file in Omeka.
     */
    protected function _transferFile($fileInfo, $originalFilename)
    {
        // Upload a single file at a time.
        if (!$this->_adapter->receive($fileInfo['form_index'])) {
            throw new Omeka_File_Ingest_InvalidException(join("\n\n", $this->_adapter->getMessages()));
        }

        // Return the path to the file as it is listed in Omeka.
        return $this->_adapter->getFileName($fileInfo['form_index']);
    }

    /**
     * Use the adapter to extract the array of file information.
     * 
     * @param string|null $fileInfo The name of the form input to ingest.
     * @return array
     */
    protected function _parseFileInfo($fileInfo)
    {
        if (!$this->_adapter) {
            $this->_buildAdapter();
        }

        if (is_array($fileInfo) && array_key_exists('source', $fileInfo)) {
            $fileKey = $fileInfo['source'];
        } else {
            $fileKey = $fileInfo;
        }

        if (!is_string($fileKey)) {
            throw new Omeka_File_Ingest_Exception('File info is incorrectly formatted!');
        }

        // Grab the info from $_FILES array (prior to receiving the files).
        $fileInfoArray = $this->_adapter->getFileInfo($fileKey);

        // Include the index of the form so that we can use that if necessary.
        foreach ($fileInfoArray as $index => $info) {
            // We need the index of this as well b/c the file info is passed
            // around (not the form index).
            $info['form_index'] = $index;
            $fileInfoArray[$index] = $info;
        }

        // Only if the upload represents a single file, pass along extra
        // file data, if given
        if (is_array($fileInfo) && count($fileInfoArray) === 1) {
            if (array_key_exists('metadata', $fileInfo)) {
                $fileInfoArray[$index]['metadata'] = $fileInfo['metadata'];
            }
            if (array_key_exists('order', $fileInfo)) {
                $fileInfoArray[$index]['order'] = $fileInfo['order'];
            }
        }
        return $fileInfoArray;
    }

    /**
     * Use the Zend Framework adapter to handle validation instead of the 
     * built-in _validateFile() method.
     * 
     * @see Omeka_File_Ingest_AbstractIngest::_validateFile()
     * @param Zend_Validate_Interface $validator
     */
    public function addValidator(Zend_Validate_Interface $validator)
    {
        if (!$this->_adapter) {
            $this->_buildAdapter();
        }

        $this->_adapter->addValidator($validator);
    }
}
