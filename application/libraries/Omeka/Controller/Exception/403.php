<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * If thrown by a controller, this exception will be caught within the
 * ErrorController, which will then render a 403 Forbidden page.
 * 
 * @see ErrorController::errorAction()
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Exception_403 extends Exception {}
