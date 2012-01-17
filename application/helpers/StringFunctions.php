<?php
/**
 * All String helper functions
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage StringHelpers
 */

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
 * Escape the value for use in javascript.
 *
 * This is a convenience function for encoding a value using JSON notation.
 * Must be used when interpolating PHP output in javascript.
 *
 * Note on usage: do not wrap the resulting output of this function in quotes,
 * as proper JSON encoding will take care of that.
 */
function js_escape($value)
{
    return Zend_Json::encode($value);
}

/**
 * Escape the value for use in XML.
 *
 * @param string $value
 * @return string
 */
function xml_escape($value)
{
    return htmlspecialchars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '',
        $value), ENT_QUOTES);
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
 */
function nls2p($str)
{
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');
}

/**
 * Retrieve a substring of a given piece of text.
 *
 * Note: this will only split strings on the space character.
 * this will also strip html tags from the text before getting a snippet
 *
 * @param string $text Text to take snippet of
 * @param int $startPos Starting position of snippet in string
 * @param int $endPos Maximum length of snippet
 * @param string $append String to append to snippet if truncated
 * @return string Snippet of given text
 */
function snippet($text, $startPos, $endPos, $append = '…')
{
    // strip html tags from the text
    $text = strip_formatting($text);

    $textLength = strlen($text);

    // Calculate the start position. Set to zero if the start position is
    // null or 0, OR if the start offset is greater than the length of the
    // original text.
    $startPosOffset = $startPos - $textLength;
    $startPos = !$startPos || $startPosOffset > $textLength
                ? 0
                : strrpos($text, ' ', $startPosOffset);

    // Calculate the end position. Set to the length of the text if the
    // end position is greater than or equal to the length of the original
    // text, OR if the end offset is greater than the length of the
    // original text.
    $endPosOffset = $endPos - $textLength;
    $endPos = $endPos >= $textLength || $endPosOffset > $textLength
              ? $textLength
              : strrpos($text, ' ', $endPosOffset);

    // Set the snippet by getting its substring.
    $snippet = substr($text, $startPos, $endPos - $startPos);

    // Return the snippet without the append string if the text's original
    // length equals to 1) the length of the snippet, i.e. when the return
    // string is identical to the passed string; OR 2) the calculated
    // end position, i.e. when the return string ends at the same point as
    // the passed string.
    return strlen($snippet) == $textLength || $endPos == $textLength
            ? $snippet
            : $snippet . $append;
}

/**
 * Retrieve a substring of the text by limiting the word count.
 * Note: it strips the HTML tags from the text before getting the snippet
 *
 * @since 0.10
 * @param string $text
 * @param integer $maxWords
 * @param string $ellipsis Optional '...' by default.
 * @return string
 */
function snippet_by_word_count($text, $maxWords = 20, $ellipsis = '...')
{
    // strip html tags from the text
    $text = strip_formatting($text);

    if ($maxWords > 0) {
        $textArray = explode(' ', $text);
        if (count($textArray) > $maxWords) {
            $text = implode(' ', array_slice($textArray, 0, $maxWords)) . $ellipsis;
        }
    } else {
        return '';
    }
    return $text;
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
 */
function text_to_id($text, $prepend=null, $delimiter='-')
{
    $text = strtolower($text);
    $id = preg_replace('/\s/', $delimiter, $text);
    $id = preg_replace('/[^\w\-]/', '', $id);
    $id = trim($id, $delimiter);
    $prepend = (string) $prepend;
    return !empty($prepend) ? join($delimiter, array($prepend, $id)) : $id;
}

/**
 * Converts any URLs in a given string to links.
 *
 * @since 1.4
 * @param string $str The string to be searched for URLs to convert to links.
 * @return string
 */
function url_to_link($str)
{
    $pattern = "/(\bhttps?:\/\/\S+\b)/e";
    $replace = '"<a href=\"".htmlspecialchars("$1")."\">$1</a>"';
    $str = preg_replace($pattern, $replace, $str);
    return $str;
}
