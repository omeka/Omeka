<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Generates JSON version of the omeka-xml output, as dictated by the JsonML
 * XSLT.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Omeka_Output_Json
{
    /**
     * JsonML XML stylesheet location
     */
    const JSONML_XSLT_URL = 'http://jsonml.org/JsonML.xslt';
    
    /**
     * Convert omeka-xml output to JSON.
     *
     * @param Omeka_Output_Xml $omekaXml
     * @return string
     */
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
