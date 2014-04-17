<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Create derivative images for a file in Omeka.
 * 
 * @package Omeka\File\Derivative
 */
class Omeka_File_Derivative_Creator
{
    /**
     * @var Omeka_File_Derivative_StrategyInterface
     */
    private $_strategy;

    private $_derivatives = array();

    /**
     * @var array|null
     */
    private $_typeBlacklist;

    /**
     * @var array|null
     */
    private $_typeWhitelist;
    
    /**
     * Create all the derivatives requested with addDerivative().
     * 
     * @param string $sourcePath
     * @param string $derivFilename
     * @param string $mimeType
     * @return boolean
     */
    public function create($sourcePath, $derivFilename, $mimeType)
    {
        if (!$this->_strategy) {
            throw new Omeka_File_Derivative_Exception('No strategy has been configured.');
        }
        
        if (empty($derivFilename) || !is_string($derivFilename)) {
            throw new InvalidArgumentException("Invalid derivative filename.");
        }
        
        if (!is_readable($sourcePath)) {
            throw new RuntimeException("File at '$sourcePath' is not readable.");
        }
        
        if (!$this->_isDerivable($sourcePath, $mimeType)) {
            return false;
        }
        
        // If we have no derivative images to generate, signal nothing was done.
        if (empty($this->_derivatives)) {
            return false;
        }
                
        $workingDir = dirname($sourcePath);
        if (empty($workingDir) || !is_string($workingDir)) {
            throw new InvalidArgumentException("Invalid derivative working path.");
        }
        
        if (!(is_dir($workingDir) && is_writable($workingDir))) {
            throw new RuntimeException("Derivative working directory '$workingDir' is not writable.");
        }

        foreach ($this->_derivatives as $type => $sizeConstraint) {
            $destPath = rtrim($workingDir, DIRECTORY_SEPARATOR ) 
                         . DIRECTORY_SEPARATOR . $type . '_' . $derivFilename;
            if (!$this->_strategy->createImage($sourcePath, $destPath, $type, $sizeConstraint, $mimeType)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Add a derivative image to be created.
     * 
     * @param string $storageType
     * @param integer $size The size constraint for the image, meaning it will
     * have that maximum width or height, depending on whether the image is
     *  landscape or portrait.
     */
    public function addDerivative($storageType, $size)
    {
        if (!preg_match('/^\w+$/', $storageType)) {
            throw new InvalidArgumentException("Invalid derivative type given: '$storageType' "
                . "must be alphanumeric string.");
        }
        if (empty($size)) {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }

        if (is_numeric($size)) {
            $this->_derivatives[$storageType] = (int) $size;
        } else {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }
    }

    /**
     * Set the strategy for creating derivatives.
     *
     * @param Omeka_File_Derivative_StrategyInterface $strategy
     */
    public function setStrategy(Omeka_File_Derivative_StrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
    }

    /**
     * Get the strategy for creating derivatives.
     *
     * @return Omeka_File_Derivative_StrategyInterface
     */
    public function getStrategy()
    {
        return $this->_strategy;
    }

    /**
     * Set the type blacklist.
     *
     * @param array|null $blacklist An array of mime types to blacklist.
     */
    public function setTypeBlacklist($blacklist)
    {
        $this->_typeBlacklist = $blacklist;
    }

    /**
     * Set the type whitelist.
     *
     * @param array|null $whitelist An array of mime types to whitelist.
     */
    public function setTypeWhitelist($whitelist)
    {
        $this->_typeWhitelist = $whitelist;
    }

    /**
     * Returns whether Omeka can make derivatives of the given file.
     *
     * The file must be readable and pass the mime whitelist/blacklist.
     * 
     * @param string $filePath
     * @param string $mimeType
     * @return boolean
     */
    private function _isDerivable($filePath, $mimeType)
    {
        return (is_readable($filePath) 
                && $this->_passesBlacklist($mimeType)
                && $this->_passesWhitelist($mimeType));
    }

    /**
     * Return whether the given type is allowed by the blacklist.
     *
     * If no blacklist is specified all types will pass.
     *
     * @param string $mimeType
     * @return bool
     */
    private function _passesBlacklist($mimeType)
    {
        if (!isset($this->_typeBlacklist)) {
            return true;
        }

        return !in_array($mimeType, $this->_typeBlacklist);
    }

    /**
     * Return whether the given type is allowed by the whitelist.
     *
     * If no whitelist is specified all types will pass, but an
     * empty whitelist will reject all types.
     *
     * @param string $mimeType
     * @return bool
     */
    private function _passesWhitelist($mimeType)
    {
        if (!isset($this->_typeWhitelist)) {
            return true;
        }

        return in_array($mimeType, $this->_typeWhitelist);
    }
}
