<?php

/**
 * Converts any URLs in a given string to links.
 *
 * @param string $str The string to be searched for URLs to convert to links.
 * @return string
 **/
function url_to_link($str)
{
    $pattern = "@\b(https?://)?(([0-9a-zA-Z_!~*'().&=+$%-]+:)?[0-9a-zA-Z_!~*'().&=+$%-]+\@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-zA-Z_!~*'()-]+\.)*([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z]\.[a-zA-Z]{2,6})(:[0-9]{1,4})?((/[0-9a-zA-Z_!~*'().;?:\@&=+$,%#-]+)*/?)@";
    $str = preg_replace($pattern, '<a href="\0">\0</a>', $str);
    return $str;
}
