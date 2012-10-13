<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * If thrown by a controller, this exception will be caught within the
 * ErrorController, which will then render a 403 Forbidden page.
 * 
 * @package Omeka\Controller
 */
class Omeka_Controller_Exception_403 extends Exception {}
