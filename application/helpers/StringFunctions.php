<?php
/**
 * All String helper functions
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage StringHelpers
 **/

/**
 * Escape the value to display properly as HTML.
 * 
 * This uses the 'html_escape' filter for escaping.
 * 
 * @param string
 * @return string
 */
function html_escape($value)
{
    return apply_filters('html_escape', $value);
}

/**
 * Replace new lines in a block of text with paragraph tags.
 * 
 * Looks for 2 consecutive line breaks resembling a paragraph break and wraps
 * each of the paragraphs with a <p> tag.  If no paragraphs are found, then the
 * original text will be wrapped with line breaks.
 * 
 * @link http://us.php.net/manual/en/function.nl2br.php#73479
 * @param string $str
 * @return string
 **/
function nls2p($str)
{
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');
}

/**
 * Retrieve a substring of a given piece of text. 
 * 
 * Note that this will only split strings on the space character.
 * 
 * @param string $text
 * @param int $startPos
 * @param int $endPos
 * @param string $append
 * @return string
 **/
function snippet($text, $startPos, $endPos, $append = '…')
{
    $startPos = ( !$startPos ) ? 0 : strrpos( $text, ' ', $startPos - strlen($text) ); 
    $endPos = strrpos( $text, ' ', ( $endPos ) - strlen($text) );
    if (!$endPos) {
        $endPos = strlen($text);  
    } 
    $snippet = substr($text, $startPos, $endPos - $startPos);
	if (strlen($snippet)) {
		return  $snippet . $append; 
	}
}

/**
 * Retrieve a substring of the text by limiting the word count.
 * 
 * @since 0.10
 * @param string $phrase
 * @param integer $maxWords
 * @param string $ellipsis Optional '...' by default.
 * @return string
 **/
function snippet_by_word_count($phrase, $maxWords, $ellipsis = '...')
{
    $phraseArray = explode(' ', $phrase);
    if (count($phraseArray) > $maxWords && $maxWords > 0) {
        $phrase = implode(' ', array_slice($phraseArray, 0, $maxWords)) . $ellipsis;
    }
    return $phrase;
}

/**
 * Strip HTML formatting (i.e. tags) from the provided string.
 *
 * This is essentially a wrapper around PHP's strip_tags() function, with the 
 * added benefit of returning a fallback string in case the resulting stripped 
 * string is empty or contains only whitespace.
 * 
 * @since 0.10
 * @uses strip_tags()
 * @param string $str The string to be stripped of HTML formatting.
 * @param string $allowableTags The string of tags to allow when stripping tags.
 * @param string $fallbackStr The string to be used as a fallback.
 * @return The stripped string.
 */
function strip_formatting($str, $allowableTags = '', $fallbackStr = '')
{
    // Strip the tags.
    $str = strip_tags($str, $allowableTags);
    // Remove non-breaking space html entities.
    $str = str_replace('&nbsp;', '', $str);
    // If only whitepace remains, return the fallback string.
    if (preg_match('/^\s*$/', $str)) {
        return $fallbackStr;
    }
    // Return the deformatted string.
    return $str;
}

/**
 * Converts a word or phrase to dashed format, i.e. Foo Bar => foo-bar
 *
 * This is primarily for easy creation of HTML ids within Omeka
 *
 * 1) convert to lowercase
 * 2) Replace whitespace with -, 
 * 3) remove all non-alphanumerics, 
 * 4) remove leading/trailing delimiters
 * 5) optionally prepend a piece of text
 *
 * @param string $text The text to convert
 * @param string $prepend Another string to prepend to the ID
 * @param string $delimiter The delimiter to use (- by default)
 * @return string
 **/
function text_to_id($text, $prepend=null, $delimiter='-')
{
	$text = strtolower($text);
	$id = preg_replace('/\s/', $delimiter, $text);
	$id = preg_replace('/[^\w\-]/', '', $id);
	$id = trim($id, $delimiter);
	$prepend = (string) $prepend;
	return !empty($prepend) ? join($delimiter, array($prepend, $id)) : $id;
}