<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Strategy for making derivatives with the Imagick PHP extension.
 *
 * The strategy requires ext/imagick.
 *
 * @package Omeka\File\Derivative\Strategy
 */
class Omeka_File_Derivative_Strategy_Imagick
    extends Omeka_File_Derivative_AbstractStrategy
{
    /**
     * Check for the imagick extension at creation.
     *
     * @throws Omeka_File_Derivative_Exception
     */
    public function __construct()
    {
        if (!extension_loaded('imagick')) {
            throw new Omeka_File_Derivative_Exception('This derivative strategy requires ext/imagick.');
        }
    }

    /**
     * Generate a derivative image with Imagick.
     */
    public function createImage($sourcePath, $destPath, $type, $sizeConstraint, $mimeType)
    {
        try {
            $imagick = new Imagick($sourcePath . '[0]');
        } catch (ImagickException $e) {
            _log("Imagick failed to open the file. Details:\n$e", Zend_Log::ERR);
            return;
        }

        $imagick->setBackgroundColor('white');
        $imagick = $imagick->flattenImages();

        if ($type != 'square_thumbnail') {
            $imagick->thumbnailImage($sizeConstraint, $sizeConstraint, true);
        } else {
            $imagick->cropThumbnailImage($sizeConstraint, $sizeConstraint);

            // Newer Imagick versions do this automatically, but we'll also do it
            // to account for older versions.
            $imagick->setImagePage($sizeConstraint, $sizeConstraint, 0, 0);
        }

        $imagick->writeImage($destPath);
        $imagick->clear();
    }
}
