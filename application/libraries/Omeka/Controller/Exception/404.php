<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * If thrown within a controller, this will be caught in the ErrorController,
 * which will render a 404 Not Found page.
 * 
 * @package Omeka\Controller
 */
class Omeka_Controller_Exception_404 extends Exception {}
