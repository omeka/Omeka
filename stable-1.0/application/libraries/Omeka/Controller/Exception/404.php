<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * If thrown within a controller, this will be caught in the ErrorController,
 * which will render a 404 page.
 * 
 * @see ErrorController::errorAction(), Omeka_Controller_Exception_403
 * @package Omeka
 * @author CHNM
 **/
class Omeka_Controller_Exception_404 extends Exception {}