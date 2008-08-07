<?php
/**
* 
*/
class Omeka_View_Helper_Url extends Zend_View_Helper_Url
{
    public function url($options = array(), $name = null, array $queryParams=array(), $reset = false, $encode = true)
    {
        $url = '';

        $front = Zend_Controller_Front::getInstance();
        
        //If it's a string, just append it
        if(is_string($options)) {
            $url = rtrim($front->getBaseUrl(), '/') . '/';
        	$url .= $options;
        }
        //If it's an array, assemble the URL with Zend_View_Helper_Url
        elseif(is_array($options)) {
            $url = parent::url($options, $name, $reset, $encode);
        }

        if($queryParams) {
            $url .= '?' . http_build_query($queryParams, '', '&amp;');
        }

        return $url;        
    }
}
