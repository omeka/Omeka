<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Strategy for making derivatives with ImageMagick on the command line.
 * 
 * @package Omeka\File\Derivative\Strategy
 */
class Omeka_File_Derivative_Strategy_ExternalImageMagick
    extends Omeka_File_Derivative_AbstractStrategy
{
    const IMAGEMAGICK_CONVERT_COMMAND = 'convert';

    private $_convertPath;

    /**
     * Generate a derivative image from an existing file stored in Omeka.  
     * 
     * This image will be generated based on a constraint given in pixels.  For 
     * example, if the constraint is 500, the resulting image file will be scaled 
     * so that the largest side is 500px. If the image is less than 500px on both 
     * sides, the image will not be resized.
     * 
     * Derivative images will only be generated for files with mime types
     * that pass any configured blacklist and/or whitelist and can be processed
     * by the convert binary.
     * 
     * @throws Omeka_File_Derivative_Exception
     * @param string Command line arguments to the ImageMagick binary. 
     * It assumes these command line arguments are already escaped as shell arguments
     */
    public function createImage($sourcePath, $destPath, $type, $sizeConstraint, $mimeType)
    {
        $convertPath = $this->_getConvertPath();
        $convertArgs = $this->_getConvertArgs($type, $sizeConstraint);
        $page = (int) $this->getOption('page', 0);
        $cmd = join(' ', array(
            escapeshellcmd($convertPath),
            escapeshellarg($sourcePath . '[' . $page . ']'),
            $convertArgs,
            escapeshellarg($destPath)
        ));
        
        self::executeCommand($cmd, $status, $output, $errors);

        if (!empty($errors)) {
            _log("Error output from ImageMagick:\n$errors", Zend_Log::WARN);
        }

        if ($status) {
            _log("ImageMagick failed with status code $status.", Zend_Log::ERR);
            return false;
        }

        return true;
    }
    
    /**
     * Get the full path to the ImageMagick 'convert' command.
     * 
     * @throws Omeka_File_Derivative_Exception When the path is not a valid directory.
     * @return string
     */
    protected function _getConvertPath()
    {
        // Assert that this is both a valid path and a directory (cannot be a 
        // script).
        if (!empty($this->_convertPath)) {
            return $this->_convertPath;
        }
        
        $path = $this->getOption('path_to_convert');
        if ($path && ($pathClean = realpath($path)) && is_dir($pathClean)) {
            $pathClean = rtrim($pathClean, DIRECTORY_SEPARATOR);
            $this->_convertPath = $pathClean . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_CONVERT_COMMAND;
            return $this->_convertPath;
        } else {
            throw new Omeka_File_Derivative_Exception('ImageMagick is not properly configured: invalid directory given for the ImageMagick command!');
        }
    }

    /**
     * Get the ImageMagick command line for resizing to the given constraints.
     *
     * @param string $type Type of derivative being made.
     * @param int $constraint Maximum side length in pixels.
     * @return string
     */
    protected function _getConvertArgs($type, $constraint)
    {
        $version = $this->getOption('version', '0');

        if ($type != 'square_thumbnail') {
            return '-background white -flatten -thumbnail ' . escapeshellarg("{$constraint}x{$constraint}>");
        } else {
            $gravity = $this->getOption('gravity', 'Center');
            // Native square thumbnail resize requires at least version 6.3.8-3.
            if (version_compare($version, '6.3.8-3', '>=')) {
                $args = array(
                    '-background white',
                    '-flatten',
                    '-thumbnail ' . escapeshellarg("{$constraint}x{$constraint}^"),
                    '-gravity ' . escapeshellarg($gravity),
                    '-crop ' . escapeshellarg("{$constraint}x{$constraint}+0+0"),
                    '+repage'
                );
            } else {
                $args = array(
                    '-thumbnail ' . escapeshellarg('x' . $constraint*2),
                    '-resize ' . escapeshellarg($constraint*2 . 'x<'),
                    '-resize 50%',
                    '-background white',
                    '-flatten',
                    '-gravity ' . escapeshellarg($gravity),
                    '-crop ' . escapeshellarg("{$constraint}x{$constraint}+0+0"),
                    '+repage'
                );
            }
            return join (' ', $args);
        }
    }

    /**
     * Determine whether or not the path given to ImageMagick is valid.
     * The convert binary must be within the directory and executable.
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
        $convertPath = $imPath . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_CONVERT_COMMAND;
        
        // Make sure the convert file is executable
        if (!is_executable($convertPath)) {
            return false;
        }
                        
        // Attempt to run the ImageMagick binary with the version argument
        // If you try to run it without any arguments, it returns an error code
        $cmd = $convertPath . ' -version';
        
        self::executeCommand($cmd, $status, $output, $errors);
        
        // A return value of 0 indicates the convert binary is working correctly.
        return $status == 0;
    }


    /**
     * Retrieve the path to the directory containing ImageMagick's convert utility.
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
        $cmd = 'which ' . self::IMAGEMAGICK_CONVERT_COMMAND;
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
