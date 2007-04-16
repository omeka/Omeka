<?php
/**
 * Adapted from http://www.prolifique.com/entities.php.txt
 *
 * @author cameron at prolifique dot com
 * @contributors Kris Kelly
 * @version $Id$
 * @package unicode 
 **/

// Convert str to UTF-8 (if not already), then convert that to HTML named entities.
// and numbered references. Compare to native htmlentities() function.
// Unlike that function, this will skip any already existing entities in the string.
// mb_convert_encoding() doesn't encode ampersands, so use makeAmpersandEntities to convert those.
// mb_convert_encoding() won't usually convert to illegal numbered entities (128-159) unless
// there's a charset discrepancy, but just in case, correct them with correctIllegalEntities.
function allhtmlentities($str, $convertTags = 1, $encoding = "", $named_entities = 1) {
	
	//If the mb_string library is not installed, there's nothing we can do except use htmlentities
	if( !function_exists('mb_convert_encoding') ) return htmlentities($str); 

 if (is_array($arrOutput = $str)) {
    foreach (array_keys($arrOutput) as $key)
      $arrOutput[$key] = allhtmlentities($arrOutput[$key], $convertTags, $encoding, $named_entities);
    return $arrOutput;
    }
  else if ($str === 0 or !empty($str)) {

    $str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
    
	//Convert the ampersand before converting tags
	$str = preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $named_entities ? "&amp;" : "&#38;", $str);
    
	if ($convertTags)
	{
  		// Note that we should use &apos; for the single quote, but IE doesn't like it
      	$arrReplace = $named_entities ? array('&#39;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;');
  		$str = str_replace(array("'",'"','<','>'), $arrReplace, $str);
	}
    
	$str = correctEntities($str);
    return $str;
  }
}

/**
 * Correct illegal entities into their legal counterparts
 *
 * @return string
 **/
function correctEntities( $str )
{
	$illegal = array(
    '/&#128;/','/&#130;/','/&#131;/','/&#132;/','/&#133;/','/&#134;/','/&#135;/','/&#136;/',
	'/&#137;/','/&#138;/','/&#139;/','/&#140;/','/&#142;/','/&#145;/','/&#146;/','/&#147;/',
    '/&#148;/','/&#149;/','/&#150;/','/&#151;/','/&#152;/','/&#153;/','/&#154;/','/&#155;/',
    '/&#156;/','/&#158;/','/&#159;/' );

	$correct = array(
     '&#8364;','&#8218;','&#402;','&#8222;','&#8230;','&#8224;','&#8225;','&#710;',
	 '&#8240;','&#352;','&#8249;','&#338;','&#381;','&#8216;','&#8217;','&#8220;',
     '&#8221;','&#8226;','&#8211;','&#8212;','&#732;','&#8482;','&#353;','&#8250;',
     '&#339;','&#382;','&#376;');

	return preg_replace( $illegal, $correct, $str);
}

?>