<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * API exception.
 * 
 * API implementers should throw this exception for controller errors.
 * 
 * @package Omeka\Controller
 */
class Omeka_Controller_Exception_Api extends Exception {
    
    /**
     * @param string $message
     * @param int $code
     * @param array $errors Custom errors
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, (int) $code);
    }
}
