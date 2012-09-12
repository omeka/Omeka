<?php
class Omeka_View_Helper_GetCurrentUrl extends Zend_View_Helper_Abstract
{
    /**
     * Return the current URL with query parameters appended.
     *
     * @param array $params
     * @return string
     */
    public function getCurrentUrl(array $params = array())
    {
        // Get the URL before the ?.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $urlParts = explode('?', $request->getRequestUri());
        $url = $urlParts[0];
        if ($params) {
            // Merge $_GET and passed parameters to build the complete query.
            $query = array_merge($_GET, $params);
            $queryString = http_build_query($query);
            $url .= "?$queryString";
        }
        return $url;
    }
}
