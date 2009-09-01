<?php
class Omeka_Output_Json
{
    const JSONML_XSLT_URL = 'http://jsonml.org/JsonML.xslt';
    
    public static function toJson(Omeka_Output_Xml $omekaXml)
    {
        $xsldoc = new DOMDocument();
        $xsldoc->loadXML(file_get_contents(self::JSONML_XSLT_URL));
        
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($xsldoc);
        
        $omekaJson = $xsl->transformToXML($omekaXml->getDoc());

        // Enable JSONP if a callback was given.
        if (isset($_GET[Omeka_Controller_Plugin_Jsonp::CALLBACK_KEY])) {
            $callback = $_GET[Omeka_Controller_Plugin_Jsonp::CALLBACK_KEY];
            $omekaJson =  "$callback($omekaJson)";
        }
        
        return $omekaJson;
    }
}