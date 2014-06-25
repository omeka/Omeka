<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for pluggable file derivative creation strategies.
 *
 * @package Omeka\File\Derivative\Strategy
 */
interface Omeka_File_Derivative_StrategyInterface
{
    /**
     * Create an derivative of the given image.
     *
     * @param string $sourcePath Local path to the source file.
     * @param string $destPath Local path to write the derivative to.
     * @param string $type The type of derivative being created.
     * @param int $sizeConstraint Size limitation on the derivative.
     * @param string $mimeType MIME type of the original file.
     * @return bool
     */
    public function createImage($sourcePath, $destPath, $type, $sizeConstraint, $mimeType);

    /**
     * Set options for the derivative strategy.
     *
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Get the options for the strategy.
     *
     * @return array
     */
    public function getOptions();
}
