<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * This controller plugin sets the default Content-Type header when one hasn't
 * been set at the end of the controller processing.
 *
 * This has to be done here because Zend allows header duplication, the
 * output contexts don't overwrite headers of the same name, and some servers
 * (FastCGI) choke when they see two Content-Type headers.
 * 
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_DefaultContentType extends Zend_Controller_Plugin_Abstract
{
    /**
     * Add a default Content-Type to the response if none is already set.
     */
    public function dispatchLoopShutdown()
    {
        $response = $this->getResponse();

        $typeAlreadySet = false;
        foreach ($response->getHeaders() as $header) {
            if ($header['name'] == 'Content-Type') {
                $typeAlreadySet = true;
                break;
            }
        }

        if (!$typeAlreadySet) {
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        }
    }
}
