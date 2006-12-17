<?php
/**
 * Global functions
 * Available in all classes and themes
 */
function issetor(&$foo, $bar) {
	return isset($foo) ? $foo : $foo = $bar;
}

function notemptyor(&$foo, $bar) {
	return !empty($foo) ? $foo : $foo = $bar;
}

// in case mime_content_type doesn't exist, define it
if (!function_exists('mime_content_type')) {
	function mime_content_type($f)
	{
		return exec(trim('file -bi ' . escapeshellarg($f)));
	}
}

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
  else if (!empty($str)) {

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
 * @return void
 * @author Kris Kelly
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



// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           // ASCII
       | [\xC2-\xDF][\x80-\xBF]            // non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        // excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} // straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        // excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     // planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         // planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     // plane 16
   )*$%xs', $str);
}

?>