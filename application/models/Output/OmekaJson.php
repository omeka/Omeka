<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generates JSON version of the omeka-xml output, as dictated by the JsonML
 * XSLT.
 * 
 * @package Omeka\Output
 */
class Output_OmekaJson
{
    /**
     * JsonML XML stylesheet filename
     */
    const JSONML_XSLT_FILENAME = 'JsonML.xslt';
    
    /**
     * Convert omeka-xml output to JSON.
     *
     * @param Output_OmekaXmlAbstract $omekaXml
     * @return string
     */
    public static function toJson(Omeka_Output_OmekaXml_AbstractOmekaXml $omekaXml)
    {
        $xsltPath = dirname(__FILE__) . '/' . self::JSONML_XSLT_FILENAME;

        $xsldoc = new DOMDocument();
        $xsldoc->load($xsltPath);
        
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
