<?php 
// When displaying item metadata (or anything else that makes use of the 
// 'html_escape' filter), make sure we escape entities correctly with UTF-8.
add_filter('html_escape', 'utf8_htmlentities', 1);

if (!function_exists('utf8_htmlentities')) {
    function utf8_htmlentities($value)
    {
        return htmlentities($value, ENT_QUOTES, "UTF-8");
    }
}

// The second thing that needs to happen for all metadata that needs to be escaped
// to HTML is that new lines need to be represented as <br /> tags.
add_filter('html_escape', 'nl2br', 2);