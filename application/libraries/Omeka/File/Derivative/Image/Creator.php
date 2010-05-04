<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Create derivative images for a file in Omeka.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_File_Derivative_Image_Creator
{
    const IMAGEMAGICK_COMMAND = 'convert';
    
    const DERIVATIVE_EXT = 'jpg';
    
    private $_cmdPath;
    
    private $_derivatives = array();
    
    /**
     * @var array List of mime-types which have known problems with ImageMagick
     * and still return dimensions when called w/ getimagesize().
     */
    private $_mimeTypeBlacklist = array(
        'application/x-shockwave-flash', 
        'image/jp2'
    );
    
    public function __construct($convertDir)
    {
        $this->setConvertPath($convertDir);
    }
    
    /**
     * Set the path to the ImageMagick executable.
     * 
     * @since 2.0
     * @param string $dir Path to the directory containing the ImageMagick binary.
     * @throws Omeka_File_Derivative_Exception When the path is not a valid directory.
     **/
    public function setConvertPath($dir)
    {
        // Assert that this is both a valid path and a directory (cannot be a 
        // script).
        if (($cleanPath = realpath($dir)) && is_dir($dir)) {
            $imPath = rtrim($cleanPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_COMMAND;
            $this->_cmdPath = $imPath;
        } else {
            throw new Omeka_File_Derivative_Exception('ImageMagick is not properly configured: invalid directory given for the ImageMagick command!');
        }
    }
    
    /**
     * @since 2.0
     */
    public function getConvertPath()
    {
        return $this->_cmdPath;
    }
    
    /**
     * @since 2.0
     * @param string $fromFilePath
     * @param string $derivFilename
     * @param string $mimeType
     * @return boolean
     */
    public function create($fromFilePath, $derivFilename, $mimeType)
    {
        if (!is_string($derivFilename) || $derivFilename == null) {
            throw new InvalidArgumentException("Invalid derivative filename given.");
        }
        
        if (!file_exists($fromFilePath)) {
            throw new RuntimeException("File at '$fromFilePath' does not exist.");
        }
        
        if (!is_readable($fromFilePath)) {
            throw new RuntimeException("File at '$fromFilePath' is not readable.");
        }
        
        if (!$this->_isDerivable($fromFilePath, $mimeType)) {
            return false;
        }
        
        // If we have no derivative images to generate, signal nothing was done.
        if (empty($this->_derivatives)) {
            return false;
        }
                
        foreach ($this->_derivatives as $storageDir => $cmdArgs) {
            $newFilePath = rtrim($storageDir, DIRECTORY_SEPARATOR ) 
                         . DIRECTORY_SEPARATOR . $derivFilename;
            $this->_createImage($fromFilePath, $newFilePath, $cmdArgs);
        }
        
        return true;
    }
    
    /**
	 * @since 2.0
	 * @param string $storageDir
	 * @param integer|string $size If an integer, it is the size constraint for
	 * the image, meaning it will have that maximum width or height, depending
	 * on whether the image is landscape or portrait.  Otherwise, it is a string
	 * of arguments to be passed to the ImageMagick convert utility.  MUST BE 
	 * PROPERLY ESCAPED AS SHELL ARGUMENTS.
	 */
	public function addDerivative($storageDir, $size)
	{
        if (!is_string($storageDir) || empty($storageDir)) {
            throw new InvalidArgumentException("Invalid derivative storage path given.");
        }
        
        if (!is_dir($storageDir)) {
            throw new RuntimeException("Derivative storage directory does not exist: '$storageDir'.");
        }
        
        if (!is_writable($storageDir)) {
            throw new RuntimeException("Derivative storage directory is not writable");
        }
        
        if (empty($size)) {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }

        if (is_numeric($size)) {
            $this->_derivatives[$storageDir] = $this->_getDefaultResizeCmdArgs($size);
        } else if (is_string($size)) {
            $this->_derivatives[$storageDir] = $size;
        } else {
            throw new InvalidArgumentException("Invalid derivative storage size given.");
        }
	}
	
	/**
     * Generate a derivative image from an existing file stored in Omeka's archive.  
     * 
     * This image will be generated based on a constraint given in pixels.  For 
     * example, if the constraint is 500, the resulting image file will be scaled 
     * so that the largest side is 500px. If the image is less than 500px on both 
     * sides, the image will not be resized.
     * 
     * All derivative images will be JPEG, which is specified by the class 
     * constant DERIVATIVE_EXT.  
     * 
     * Derivative images will only be generated for files with mime types
	 * that are not listed on the isDerivable static function's blacklist, and can
     * can be read by PHP's getimagesize() function.  Documentation for supported 
     * file types can be found on PHP.net's doc page for getimagesize() or 
     * image_type_to_mime_type().
     * 
     * @throws Omeka_File_Derivative_Exception
     * @param string Path to original file.
     * @param string Path to newly generated derivative file.
     * @param string Command line arguments to the ImageMagick binary.
     **/
	private function _createImage($origPath, $newPath, $convertArgs)
	{	    
	    $cmd = join(' ', array(
	        escapeshellcmd($this->_cmdPath),
	        escapeshellarg($origPath),
	        $convertArgs,
	        escapeshellarg($newPath)
	    ));
	    	    
	    exec($cmd, $output, $returnVar);
        
        if ($returnVar) {
            throw new Omeka_File_Derivative_Exception("ImageMagick error: Return Code: {$returnVar}.");
        }
	}
		
	private function _getDefaultResizeCmdArgs($constraint)
	{
	    return '-resize ' . escapeshellarg($constraint.'x'.$constraint.'>');
	}
	
    /**
     * Checks if Imagemagick is able to make derivative images of that file, based
     * upon whether or not it has image dimensions, and if it's not on a blacklist
     * of file mime-types
     * 
     * @param string
     * @param string
     * @return boolean
     **/
    private function _isDerivable($old_path, $mimeType)
    {		

    // Next we'll check that it has image dimensions, and isn't on a blacklist
    return (file_exists($old_path) 
            && is_readable($old_path) 
            && getimagesize($old_path) 
            && !(in_array($mimeType, $this->_mimeTypeBlacklist)));
    }
}