<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Output API JSON.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_JsonApi extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Prepare the JSON, send it, and exit the application.
     * 
     * @param mixed $data
     */
    public function direct($data)
    {
        $response = $this->getResponse();
        $request = $this->getRequest();

        $response->setHeader('Content-Type', 'application/json', true);

        // Add header data for JSONP requests.
        if (isset($_GET['callback'])) {
            $response->setHeader('Content-Type', 'application/javascript', true);
            $headers = array('status' => $response->getHttpResponseCode());
            $data = array('headers' => $headers, 'data' => $data);
        }

        $flags = 0;
        // Don't choke completely on invalid UTF-8 content (requires PHP 7.2+)
        if (PHP_VERSION_ID >= 70200) {
            $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        // Pretty print the JSON if requested.
        if (isset($_GET['pretty_print'])) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $json = $this->_encode($data, $flags);

        // Wrap the JSON with a callback function if requested.
        if (isset($_GET['callback'])) {
            $json = $_GET['callback'] . "($json);";
        }

        $response->setBody($json);
        $response->sendResponse();
        exit;
    }

    /**
     * Thin wrapper around json_encode to preserve Zend_Json::encode's
     * default behavior, but allow passing PHP flags to json_encode.
     *
     * Specifically this means supporting toJson and toArray methods on passed
     * objects.
     */
    private function _encode($data, $flags)
    {
        if (is_object($data)) {
            if (method_exists($data, 'toJson')) {
                return $data->toJson();
            } else if (method_exists($data, 'toArray')) {
                return $this->_encode($data->toArray(), $flags);
            }
        }

        return json_encode($data, $flags);
    }
}
