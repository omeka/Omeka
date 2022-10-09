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
class Omeka_File_Derivative_Strategy_Imagick extends Omeka_File_Derivative_AbstractStrategy
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
        $page = (int) $this->getOption('page', 0);
        try {
            $imagick = new Imagick($sourcePath . '[' . $page . ']');
        } catch (ImagickException $e) {
            _log("Imagick failed to open the file. Details:\n$e", Zend_Log::ERR);
            return false;
        }

        if ($this->getOption('autoOrient', true)) {
            $this->_autoOrient($imagick);
        }

        $origX = $imagick->getImageWidth();
        $origY = $imagick->getImageHeight();

        $imagick->setImagePage($origX, $origY, 0, 0);
        $imagick->setBackgroundColor('white');
        $imagick->setImageBackgroundColor('white');

        if (defined('Imagick::ALPHACHANNEL_REMOVE')) {
            $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        } else {
            $imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        }

        if ($type != 'square_thumbnail') {
            $imagick->thumbnailImage($sizeConstraint, $sizeConstraint, true);
        } else {
            // We could use cropThumbnailImage here but it lacks support for
            // the gravity setting
            if ($origX < $origY) {
                $newX = $sizeConstraint;
                $newY = $origY * ($sizeConstraint / $origX);
                $offsetX = 0;
                $offsetY = $this->_getCropOffsetY($newY, $sizeConstraint);
            } else {
                $newY = $sizeConstraint;
                $newX = $origX * ($sizeConstraint / $origY);
                $offsetY = 0;
                $offsetX = $this->_getCropOffsetX($newX, $sizeConstraint);
            }

            $imagick->thumbnailImage($newX, $newY);
            $imagick->cropImage($sizeConstraint, $sizeConstraint, $offsetX, $offsetY);
            $imagick->setImagePage($sizeConstraint, $sizeConstraint, 0, 0);
        }

        $imagick->writeImage($destPath);
        $imagick->clear();
        return true;
    }

    /**
     * Get the required crop offset on the X axis.
     *
     * This respects the 'gravity' setting.
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
     * This respects the 'gravity' setting.
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

    protected function _autoOrient($imagick)
    {
        $orientation = $imagick->getImageOrientation();
        $white = new ImagickPixel('#fff');
        switch ($orientation) {
            case Imagick::ORIENTATION_RIGHTTOP:
                $imagick->rotateImage($white, 90);
                break;
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $imagick->rotateImage($white, 180);
                break;
            case Imagick::ORIENTATION_LEFTBOTTOM:
                $imagick->rotateImage($white, 270);
                break;
            case Imagick::ORIENTATION_TOPRIGHT:
                $imagick->flopImage();
                break;
            case Imagick::ORIENTATION_RIGHTBOTTOM:
                $imagick->flopImage();
                $imagick->rotateImage($white, 90);
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT:
                $imagick->flopImage();
                $imagick->rotateImage($white, 180);
                break;
            case Imagick::ORIENTATION_LEFTTOP:
                $imagick->flopImage();
                $imagick->rotateImage($white, 270);
                break;
            case Imagick::ORIENTATION_TOPLEFT:
            default:
                break;
        }
    }
}
