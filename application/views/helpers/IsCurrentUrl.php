<?php
class Omeka_View_Helper_IsCurrentUrl extends Zend_View_Helper_Abstract
{
    public function isCurrentUrl($url)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $currentUrl = $request->getRequestUri();
        $baseUrl = $request->getBaseUrl();
        
        // Strip out the protocol, host, base URL, and rightmost slash before 
        // comparing the URL to the current one
        $stripOut = array(WEB_DIR, @$_SERVER['HTTP_HOST'], $baseUrl);
        $currentUrl = rtrim(str_replace($stripOut, '', $currentUrl), '/');
        $url = rtrim(str_replace($stripOut, '', $url), '/');
        
        if (strlen($url) == 0) {
            return (strlen($currentUrl) == 0);
        }
        return ($url == $currentUrl) or (strpos($currentUrl, $url) === 0);
    }
}
