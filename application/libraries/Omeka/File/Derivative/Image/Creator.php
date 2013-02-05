<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Create derivative images for a file in Omeka.
 * 
 * @package Omeka\File\Derivative
 */
class Omeka_File_Derivative_Image_Creator
{
    const IMAGEMAGICK_CONVERT_COMMAND = 'convert';
    const IMAGEMAGICK_IDENTIFY_COMMAND = 'identify';

    private $_convertPath; // path to the ImageMagick convert binary
    private $_identifyPath; // path to the ImageMagick identify binary
    
    private $_derivatives = array();
        
    public function __construct($imDirPath)
    {
        $this->setImageMagickDirPath($imDirPath);
    }
    
    /**
     * Set the path to the ImageMagick executable.
     * 
     * @param string $imDirPath Path to the directory containing the ImageMagick binaries.
     * @throws Omeka_File_Derivative_Exception When the path is not a valid directory.
     */
    public function setImageMagickDirPath($imDirPath)
    {
        // Assert that this is both a valid path and a directory (cannot be a 
        // script).
        if (($imDirPathClean = realpath($imDirPath)) && is_dir($imDirPath)) {
            $imDirPathClean = rtrim($imDirPathClean, DIRECTORY_SEPARATOR);
            $this->_convertPath = $imDirPathClean . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_CONVERT_COMMAND;
            $this->_identifyPath = $imDirPathClean . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_IDENTIFY_COMMAND;
        } else {
            throw new Omeka_File_Derivative_Exception('ImageMagick is not properly configured: invalid directory given for the ImageMagick command!');
        }
    }
    
    /**
     * Get the full path to the ImageMagick 'convert' command.
     *
     * @return string
     */
    public function getConvertPath()
    {
        return $this->_convertPath;
    }
    
    /**
     * Get the full path to the ImageMagick 'identify' command.
     *
     * @return string
     */
    public function getIdentifyPath()
    {
        return $this->_identifyPath;
    }
    
    /**
     * Create all the derivatives requested with addDerivative().
     * 
     * @param string $fromFilePath
     * @param string $derivFilename
     * @param string $mimeType
     * @return boolean
     */
    public function create($fromFilePath, $derivFilename, $mimeType)
    {
        if (empty($derivFilename) || !is_string($derivFilename)) {
            throw new InvalidArgumentException("Invalid derivative filename.");
        }
        
        if (!is_readable($fromFilePath)) {
            throw new RuntimeException("File at '$fromFilePath' is not readable.");
        }
        
        if (!$this->_isDerivable($fromFilePath)) {
            return false;
        }
        
        // If we have no derivative images to generate, signal nothing was done.
        if (empty($this->_derivatives)) {
            return false;
        }
                
        $workingDir = dirname($fromFilePath);
        if (empty($workingDir) || !is_string($workingDir)) {
            throw new InvalidArgumentException("Invalid derivative working path.");
        }
        
        if (!(is_dir($workingDir) && is_writable($workingDir))) {
            throw new RuntimeException("Derivative working directory '$workingDir' is not writable.");
        }

        foreach ($this->_derivatives as $storageType => $cmdArgs) {
            $newFilePath = rtrim($workingDir, DIRECTORY_SEPARATOR ) 
                         . DIRECTORY_SEPARATOR . $storageType . '_' . $derivFilename;
            $this->_createImage($fromFilePath, $newFilePath, $cmdArgs);
        }
        
        return true;
    }

    /**
     * Add a derivative image to be created.
     * 
     * @param string $storageType
     * @param integer|string $size If an integer, it is the size constraint for
     * the image, meaning it will have that maximum width or height, depending
     * on whether the image is landscape or portrait.  Otherwise, it is a string
     * of arguments to be passed to the ImageMagick convert utility.  MUST BE 
     * PROPERLY ESCAPED AS SHELL ARGUMENTS.
     * @param boolean $square Whether the derivative to add should be made square.
     */
    public function addDerivative($storageType, $size, $square = false)
    {
        if (!preg_match('/^\w+$/', $storageType)) {
            throw new InvalidArgumentException("Invalid derivative type given: '$storageType' "
                . "must be alphanumeric string.");
        }
        if (empty($size)) {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }

        if (is_numeric($size)) {
            $this->_derivatives[$storageType] = $this->_getResizeCmdArgs($size, $square);
        } else if (is_string($size)) {
            $this->_derivatives[$storageType] = $size;
        } else {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }
    }

    /**
     * Generate a derivative image from an existing file stored in Omeka.  
     * 
     * This image will be generated based on a constraint given in pixels.  For 
     * example, if the constraint is 500, the resulting image file will be scaled 
     * so that the largest side is 500px. If the image is less than 500px on both 
     * sides, the image will not be resized.
     * 
     * Derivative images will only be generated for files with mime types
     * that can be identified with ImageMagick's 'identify' command
     * 
     * @throws Omeka_File_Derivative_Exception
     * @param string Path to original file.
     * @param string Path to newly generated derivative file.
     * @param string Command line arguments to the ImageMagick binary. 
     * It assumes these command line arguments are already escaped as shell arguments
     */
    private function _createImage($origPath, $newPath, $convertArgs)
    {
        $cmd = join(' ', array(
            escapeshellcmd($this->_convertPath),
            escapeshellarg($origPath . '[0]'), // first page of multi-page images.
            $convertArgs,
            escapeshellarg($newPath)
        ));
        
        self::executeCommand($cmd, $status, $output, $errors);
        
        if ($status) {
            throw new Omeka_File_Derivative_Exception("ImageMagick failed with status code $status. Error output:\n$errors");
        }
        if (!empty($errors)) {
            _log("Error output from ImageMagick:\n$errors", Zend_Log::WARN);
        }
    }

    /**
     * Get the ImageMagick command line for resizing to the given constraints.
     *
     * @param integer $constraint Maximum side length in pixels.
     * @param boolean $square Whether the derivative should be squared off.
     * @return string
     */
    private function _getResizeCmdArgs($constraint, $square)
    {
        if (!$square) {
            return '-background white -flatten -thumbnail ' . escapeshellarg("{$constraint}x{$constraint}>");
        } else {
            return join(' ', array(
                '-thumbnail ' . escapeshellarg('x' . $constraint*2),
                '-resize ' . escapeshellarg($constraint*2 . 'x<'),
                '-resize 50%',
                '-background white',
                '-flatten',
                '-gravity center',
                '-crop ' . escapeshellarg("{$constraint}x{$constraint}+0+0"),
                '+repage'));
        }
    }

    /**
     * Returns true only if ImageMagick is able to make derivative images of that file based
     * upon whether or not it can be identified by ImageMagick's 'identify' binary. Otherwise returns false. 
     * 
     * @param string $filePath
     * @return boolean
     */
    private function _isDerivable($filePath)
    {
        // Next we'll check that it is identifiable by ImageMagick, and isn't on a blacklist
        return (file_exists($filePath) 
                && is_readable($filePath) 
                && $this->_isIdentifiable($filePath));
    }


    /**
     * Returns true only if the file can be identified by ImageMagick's 'identify' binary 
     * 
     * @param string $filePath
     * @return boolean
     */
    private function _isIdentifiable($filePath)
    {
        $cmd = join(' ', array(
            escapeshellcmd($this->_identifyPath),
            escapeshellarg($filePath . '[0]'), // first page of multi-page images.
        ));
        
        self::executeCommand($cmd, $status, $output, $errors);
        
        if (!empty($errors)) {
            _log("Error output from ImageMagick:\n$errors", Zend_Log::WARN);
        }
        
        return ($status == 0);
    }

    /**
     * Determine whether or not the path given to ImageMagick is valid.
     * Both the convert and identify binaries must be within the directory and executable.
     * 
     * @param string
     * @return boolean
     */
    public static function isValidImageMagickPath($dirToIm)
    {
        if (!realpath($dirToIm) || !is_dir($dirToIm)) {
            return false;
        }
        
        // Append the convert binary to the given path.
        $imPath = rtrim($dirToIm, DIRECTORY_SEPARATOR);
        $convertPath = $imPath . DIRECTORY_SEPARATOR . Omeka_File_Derivative_Image_Creator::IMAGEMAGICK_CONVERT_COMMAND;
        $identifyPath = $imPath . DIRECTORY_SEPARATOR . Omeka_File_Derivative_Image_Creator::IMAGEMAGICK_IDENTIFY_COMMAND;
        
        //Make sure the convert and identify files are both executable
        if (!is_executable($convertPath) || !is_executable($identifyPath)) {
            return false;
        }
                        
        // Attempt to run the ImageMagick binary with the version argument
        // If you try to run it without any arguments, it returns an error code
        $cmd_convert = $convertPath . ' -version';
        $cmd_identify = $identifyPath . ' -version';
        
        self::executeCommand($cmd_convert, $status_convert, $output_convert, $errors_convert);
        self::executeCommand($cmd_identify, $status_identify, $output_identify, $errors_identify);        
        
        
        // A return value of 0 indicates the convert and identify binaries are working correctly.
        return ($status_convert == 0 && $status_identify == 0);
    }


    /**
     * Retrieve the path to the directory containing ImageMagick's convert utility.
     * Th
     * 
     * Uses the 'which' command-line utility to detect the path to 'convert'. 
     * Note that this will only work if the convert utility is in PHP's PATH and
     * thus can be located by 'which'.
     * 
     * @return string The path to the directory if it can be found.  Otherwise returns an empty string.
     */
    public static function getDefaultImageMagickDir()
    {
        // Use the "which" command to auto-detect the path to ImageMagick
        $cmd = 'which ' . Omeka_File_Derivative_Image_Creator::IMAGEMAGICK_CONVERT_COMMAND;
        try {
            self::executeCommand($cmd, $status, $output, $errors);
            return $status == 0 ? dirname($output) : '';
        } catch (Omeka_File_Derivative_Exception $e) {
            return '';
        }
    }
    
    public static function executeCommand($cmd, &$status, &$output, &$errors)
    {
        // Using proc_open() instead of exec() solves a problem where exec('convert') 
        // fails with a "Permission Denied" error because the current working 
        // directory cannot be set properly via exec().  Note that exec() works 
        // fine when executing in the web environment but fails in CLI.
        $descriptorSpec = array(
            0 => array("pipe", "r"), //STDIN
            1 => array("pipe", "w"), //STDOUT
            2 => array("pipe", "w"), //STDERR
        );
        if ($proc = proc_open($cmd, $descriptorSpec, $pipes, getcwd())) {
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            foreach ($pipes as $pipe) {
                fclose($pipe);
            }
            $status = proc_close($proc);
        } else {
            throw new Omeka_File_Derivative_Exception("Failed to execute command: $cmd.");
        }
    }
}
