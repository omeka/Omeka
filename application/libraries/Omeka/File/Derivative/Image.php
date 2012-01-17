<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Creates derivative images for a file in Omeka.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 */
class Omeka_File_Derivative_Image
{
    /**
     * Command to call to create derivatives.
     */
    const IMAGEMAGICK_COMMAND = 'convert';

    /**
     * File extension of created derivatives.
     */
    const DERIVATIVE_EXT = 'jpg';

    /**
     * Check that Omeka is set up to make derivatives.
     *
     * Currently checks for the existence and validity of these options:
     * - 'fullsize_constraint'
     * - 'thumbnail_constraint'
     * - 'square_thumbnail_constraint'
     *
     * @throws Omeka_File_Derivative_Exception If Omeka can't make derivatives.
     * @return void
     */
    protected static function checkOmekaCanMakeDerivativeImages()
    {
        //Check the constraints to make sure they are valid
        $constraints = array('fullsize_constraint',
                             'thumbnail_constraint',
                             'square_thumbnail_constraint');

        foreach ($constraints as $constraint) {
            $constraint_size = get_option($constraint);

            if (!$constraint_size or !is_numeric($constraint_size)) {
                throw new Omeka_File_Derivative_Exception(
                    "The sizes for derivative images have not been configured properly." );
            }
        }
    }

    /**
     * Retrieve the directory path to the ImageMagick 'convert' executable.
     *
     * Since input is not validated on the settings form, this needs to verify
     * that the stored setting is not an arbitrary executable, but is in fact
     * just the path to the directory containing the ImageMagick executables.
     *
     * This only returns the path to the 'convert' executable, which Omeka uses
     * for generating images.
     *
     * @since 1.0 The 'path_to_convert' setting must be the directory containing
     * the ImageMagick executable, not the path to the executable itself.
     * @throws Omeka_File_Derivative_Exception When the path is not a valid directory.
     * @return string Absolute path to the ImageMagick executable.
     */
    protected static function _getPathToImageMagick()
    {
        $rawPath = get_option('path_to_convert');
        // Assert that this is both a valid path and a directory (cannot be a
        // script).
        if (($cleanPath = realpath($rawPath)) && is_dir($cleanPath)) {
            $imPath = rtrim($cleanPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_COMMAND;
            return $imPath;
        } else {
            throw new Omeka_File_Derivative_Exception('ImageMagick is not properly configured: invalid directory given for the ImageMagick command!');
        }
    }

    /**
     * Generate all the derivative images for this file.
     *
     * Currently, derivative images include:
     * - 'fullsize'
     * - 'thumbnail'
     * - 'square_thumbnail'
     * New sizes could be added if a plugin were to
     * hook into the 'after_file_upload' hook, so this method does not need to
     * be extensible.
     *
     * @param string $path File to create derivatives of.
     * @return string Derived image name.
     */
    public static function createDerivativeImages($path)
    {
        // Function processes derivatives of every image uploaded.
        // Additional images may be created using createImage function.
        // Additionally, plugin hooks allow you to add your own additional
        // image sizes [DL]

        //Retrieve the image sizes from the database
        $full_constraint = get_option('fullsize_constraint');
        $thumb_constraint = get_option('thumbnail_constraint');
        $square_thumbnail_constraint = get_option('square_thumbnail_constraint');

        // FIXME: all of these return the same value, or throw an exception if
        // failing.  Also, the value that is returned should be the full path to
        // the file (not just the filename).
        self::createImage($path, $full_constraint, 'fullsize');
        self::createImage($path, $thumb_constraint, 'thumbnail');
        $imageName = self::createImage($path, $square_thumbnail_constraint, 'square_thumbnail');

        return $imageName;
    }

    /**
     * Generate a derivative image from an existing image stored in Omeka's archive.
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
     * @param string $old_path The full path to the archived file.
     * @param string $new_dir The full path to the directory in which to create
     * the derivative image.
     * @param integer $constraint The size constraint for the image (in pixels).
     * @param string  $type The type of the image to generate (optional).  If the type
     * specified is "square", Omeka will generated a derivative image that is
     * centered and cropped to a square.  This is primarily used for generation
     * of square thumbnails, though a plugin could also take advantage of it.
     * @return string The filename of the generated image file.
     */
    public static function createImage( $old_path, $constraint, $type=null) {
        $convertPath = self::_getPathToImageMagick();

        $newFileName = self::_getFileName($old_path);

        $new_path = dirname($old_path) . '/' . $type . '_' . $newFileName;

        $old_path = escapeshellarg( $old_path . '[0]');
        $new_path = escapeshellarg( $new_path );

        if (!$constraint) {
            throw new Omeka_File_Derivative_Exception('Image creation failed - Image size constraint must be specified within application settings');
        }

        switch ($type) {
        case "square_thumbnail":
            $command = join(' ', array(
                $convertPath,
                $old_path,
                '-thumbnail ' . escapeshellarg('x' . $constraint*2),
                '-resize ' . escapeshellarg($constraint*2 . 'x<'),
                '-resize 50%',
                '-gravity center',
                '-crop ' . escapeshellarg($constraint . 'x' . $constraint . '+0+0'),
                '+repage',
                $new_path));
            break;
        default:
            $command = join(' ', array(
                $convertPath,
                $old_path,
                '-resize ' . escapeshellarg($constraint.'x'.$constraint.'>'),
                $new_path
            ));
        }

        exec($command, $result_array, $result_value);

        if ($result_value == 0) {
            return $newFileName;
        } else {
            throw new Omeka_File_Derivative_Exception('Something went wrong with image creation.  Please notify an administrator.');
        }
    }

    /**
     * Create all of the default derivative images for a specific file.
     *
     * Whether or not to create the derivative images is based on the following
     * criteria:
     * - ImageMagick is configured and working properly.
     * - Image size constraints are all properly configured.
     * - The original file exists and is readable.
     * - The original file can be read by {@link getimagesize()}.
     * - The original file's MIME type does not belong to a blacklist of MIME
     *   types that cannot be converted.
     *
     * @todo Should be able to create derivatives for all image types that
     * ImageMagick can handle, not just the ones that can be read by getimagesize().
     * @param string $originalFilePath
     * @param string $fileMimeType
     * @return string|false If successful, return the filename of the derivative
     * image, otherwise false.
     */
    public static function createAll($originalFilePath, $fileMimeType)
    {
        // Don't try to make derivative images if we don't give a path to
        // ImageMagick.
        if (!get_option('path_to_convert')) {
            return false;
        }

        self::checkOmekaCanMakeDerivativeImages();
        return  self::isDerivable($originalFilePath, $fileMimeType)
                ? self::createDerivativeImages($originalFilePath)
                : false;
    }

    /**
     * Return the derivative file name for a file.
     *
     * @param string $archiveFilename Original filename.
     * @return string
     */
    protected static function _getFileName($archiveFilename)
    {
        $filename = basename($archiveFilename);
        $newName = explode('.', $filename);
        //ensures that all generated files are jpeg
        $newName[1] = self::DERIVATIVE_EXT;
        return implode('.', $newName);
    }

    /**
     * Checks if Imagemagick is able to make derivative images of that file, based
     * upon whether or not it has image dimensions, and if it's not on a blacklist
     * of file mime-types.
     *
     * @param string $old_path
     * @param string $mimeType
     * @return boolean
     */
    public static function isDerivable($old_path, $mimeType)
    {
        // List of mime-types which have known problems with ImageMagick
        // and still return dimensions when called w/ getimagesize()
        $blackListMimeTypes = array('application/x-shockwave-flash', 'image/jp2');

        // Next we'll check that it has image dimensions, and isn't on a blacklist
        return (file_exists($old_path)
                && is_readable($old_path)
                && getimagesize($old_path)
                && !(in_array($mimeType, $blackListMimeTypes)));
    }

    /**
     * Retrieve the path to the directory containing ImageMagick's convert utility.
     *
     * Uses the 'which' command-line utility to detect the path to 'convert'.
     * Note that this will only work if the convert utility is in PHP's PATH and
     * thus can be located by 'which'.
     *
     * @return string The path to the directory.
     */
    public static function getDefaultConvertDir()
    {
        // Use the "which" command to auto-detect the path to ImageMagick;
        // redirect std error to where std input goes, which is nowhere. See:
        // http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm. If $returnVar is "0"
        // there was no error, so assign the output of the "which" command. See:
        // http://us.php.net/manual/en/function.system.php#66795.
        $command = 'which convert 2>&0';
        $lastLineOutput = exec($command, $output, $returnVar);
        // Return only the directory component of the path returned.
        return $returnVar == 0 ? dirname($lastLineOutput) : '';
    }
}
