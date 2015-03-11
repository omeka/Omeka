<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Strategy for making derivatives with the GD PHP library (default since 4.3).
 *
 * @package Omeka\File\Derivative\Strategy
 */
class Omeka_File_Derivative_Strategy_GD
    extends Omeka_File_Derivative_AbstractStrategy
{
    /**
     * Check for the imagick extension at creation.
     *
     * @throws Omeka_File_Derivative_Exception
     */
    public function __construct()
    {
        if (!extension_loaded('gd')) {
            throw new Omeka_File_Derivative_Exception('This derivative strategy requires the extension GD.');
        }
    }

    /**
     * Generate a derivative image with GD.
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function createImage($sourcePath, $destPath, $type, $sizeConstraint, $mimeType)
    {
        try {
            // Create normal (non square) thumbnail.
            if (substr($type, 0, 7) != 'square_') {
                $result = $this->_makeThumbnail($sourcePath, $destPath, $sizeConstraint);
            }
            // Create square thumbnail.
            else {
                $result = $this->_makeSquareThumbnail($sourcePath, $destPath, $sizeConstraint);
            }
        } catch (Exception $e) {
            _log("GD failed to create thumbnail of the file. Details:\n$e", Zend_Log::ERR);
            return false;
        }

        return $result;
    }

    /**
     * GD uses multiple functions to load an image, so this one manages all.
     *
     * @param string $source Path of the managed image file
     * @return false|GD image ressource
     */
    protected function _loadImageResource($source)
    {
        if (empty($source) || !is_readable($source)) {
            return false;
        }

        try {
            $result = imagecreatefromstring(file_get_contents($source));
        } catch (Exception $e) {
            _log("GD failed to open the file. Details:\n$e", Zend_Log::ERR);
            return false;
        }

        return $result;
    }

    /**
     * Make a thumbnail from source and save it at destination.
     *
     * @param string $source Path of the source.
     * @param string $destination Path of the destination.
     * @param integer $sizeConstraint Maximum size in pixels.
     * @return boolean Returns true on success or false on failure.
     */
    protected function _makeThumbnail($source, $destination, $sizeConstraint)
    {
        $sourceGD = $this->_loadImageResource($source);
        if (empty($sourceGD)) {
            return false;
        }

        $sourceWidth = imagesx($sourceGD);
        $sourceHeight = imagesy($sourceGD);

        // Source is landscape.
        if ($sourceWidth > $sourceHeight) {
            $destinationWidth = $sizeConstraint;
            $destinationHeight = round($sourceHeight * $sizeConstraint / $sourceWidth);
        }
        // Source is portrait.
        elseif ($sourceWidth < $sourceHeight) {
            $destinationWidth = round($sourceWidth * $sizeConstraint / $sourceHeight);
            $destinationHeight = $sizeConstraint;
        }
        // Source is square.
        else {
            $destinationWidth = $sizeConstraint;
            $destinationHeight = $sizeConstraint;
        }

        $sourceX = 0;
        $sourceY = 0;

        $destinationGD = imagecreatetruecolor($destinationWidth, $destinationHeight);
        $white = imagecolorallocate($destinationGD, 255, 255, 255);
        imagefill($destinationGD, 0, 0, $white);
        $result = imagecopyresampled($destinationGD, $sourceGD, 0, 0, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

        // Save resulted resource.
        if ($result) {
            $result = imagejpeg($destinationGD, $destination);
        }

        imagedestroy($sourceGD);
        imagedestroy($destinationGD);
        return $result;
    }

    /**
     * Make a square thumbnail from source and save it at destination.
     *
     * @param string $source Path of the source.
     * @param string $destination Path of the destination.
     * @param integer $sizeConstraint Maximum size in pixels.
     * @return boolean Returns true on success or false on failure.
     */
    protected function _makeSquareThumbnail($source, $destination, $sizeConstraint)
    {
        $sourceGD = $this->_loadImageResource($source);
        if (empty($sourceGD)) {
            return false;
        }

        $sourceWidth = imagesx($sourceGD);
        $sourceHeight = imagesy($sourceGD);

        // Source is landscape.
        if ($sourceWidth > $sourceHeight) {
            $sourceSize = $sourceHeight;
            $sourceX = $this->_getOffsetX($sourceWidth, $sourceSize);
            $sourceY = 0;
        }
        // Source is portrait.
        elseif ($sourceWidth < $sourceHeight) {
            $sourceSize = $sourceWidth;
            $sourceX = 0;
            $sourceY = $this->_getOffsetY($sourceHeight, $sourceSize);
        }
        // Source is square.
        else {
            $sourceSize = $sourceWidth;
            $sourceX = 0;
            $sourceY = 0;
        }

        $destinationGD = imagecreatetruecolor($sizeConstraint, $sizeConstraint);
        $white = imagecolorallocate($destinationGD, 255, 255, 255);
        imagefill($destinationGD, 0, 0, $white);
        $result = imagecopyresampled($destinationGD, $sourceGD, 0, 0, $sourceX, $sourceY, $sizeConstraint, $sizeConstraint, $sourceSize, $sourceSize);

        // Save resulted resource.
        if ($result) {
            $result = imagejpeg($destinationGD, $destination);
        }

        imagedestroy($sourceGD);
        imagedestroy($destinationGD);
        return $result;
    }

    /**
     * Get the required offset on the X axis.
     *
     * This respects the 'gravity' setting.
     *
     * @param int $width Original image width
     * @param int $size Side size of the square region being selected
     * @return int
     */
    protected function _getOffsetX($width, $size)
    {
        $gravity = strtolower($this->getOption('gravity', 'center'));
        switch ($gravity) {
            case 'northwest':
            case 'west':
            case 'southwest':
                return 0;

            case 'northeast':
            case 'east':
            case 'southeast':
                return $width - $size;

            case 'north':
            case 'center':
            case 'south':
            default:
                return (int) (($width - $size) / 2);
        }
    }

    /**
     * Get the required offset on the Y axis.
     *
     * This respects the 'gravity' setting.
     *
     * @param int $height Original image height
     * @param int $size Side size of square region being selected
     * @return int
     */
    protected function _getOffsetY($height, $size)
    {
        $gravity = strtolower($this->getOption('gravity', 'center'));
        switch ($gravity) {
            case 'northwest':
            case 'north':
            case 'northeast':
                return 0;

            case 'southwest':
            case 'south':
            case 'southeast':
                return $height - $size;

            case 'west':
            case 'center':
            case 'east':
            default:
                return (int) (($height - $size) / 2);
        }
    }
}
