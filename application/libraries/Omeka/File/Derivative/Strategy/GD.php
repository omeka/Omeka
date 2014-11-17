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
        // Page is not managed by GD.
        // $page = (int) $this->getOption('page', 0);

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
        $result = imagecopyresized($destinationGD, $sourceGD, 0, 0, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

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
            $destinationWidth = round($sourceWidth * $sizeConstraint / $sourceHeight);
            $destinationHeight = $sizeConstraint;
            $sourceX = $this->_getCropOffsetX($destinationWidth, $sizeConstraint);
            $sourceY = 0;
        }
        // Source is portrait.
        elseif ($sourceWidth < $sourceHeight) {
            $destinationHeight = round($sourceHeight * $sizeConstraint / $sourceWidth);
            $destinationWidth = $sizeConstraint;
            $sourceX = 0;
            $sourceY = $this->_getCropOffsetY($destinationHeight, $sizeConstraint);
        }
        // Source is square.
        else {
            $destinationWidth = $sizeConstraint;
            $destinationHeight = $sizeConstraint;
            $sourceX = 0;
            $sourceY = 0;
        }

        $destinationGD = imagecreatetruecolor($sizeConstraint, $sizeConstraint);
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
     * Get the required crop offset on the X axis.
     *
     * This respects the Imagick 'gravity' setting.
     *
     * @param int $resizedX Pre-crop image width
     * @param int $sizeConstraint
     * @return int
     */
    protected function _getCropOffsetX($resizedX, $sizeConstraint)
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
                return $resizedX - $sizeConstraint;

            case 'north':
            case 'center':
            case 'south':
            default:
                return (int) (($resizedX - $sizeConstraint) / 2);
        }
    }

    /**
     * Get the required crop offset on the Y axis.
     *
     * This respects the Imagick 'gravity' setting.
     *
     * @param int $resizedY Pre-crop image height
     * @param int $sizeConstraint
     * @return int
     */
    protected function _getCropOffsetY($resizedY, $sizeConstraint)
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
                return $resizedY - $sizeConstraint;

            case 'west':
            case 'center':
            case 'east':
            default:
                return (int) (($resizedY - $sizeConstraint) / 2);
        }
    }
}
