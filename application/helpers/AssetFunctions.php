<?php
/**
 * Helper functions for accessing and using theme assets.
 * 
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage AssetHelpers
 */

/**
 * Declare that a JavaScript file or files will be used on the page.
 * All "used" scripts will be included in the page's head.
 *
 * This needs to be called either before head(), or in a plugin_header hook.
 *
 * @since 1.3
 * @see head_js()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 * @return void
 */
function queue_js_file($file, $dir = 'javascripts')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_js_file($singleFile, $dir);
        }
        return;
    }
    get_view()->headScript()->appendFile(src($file, $dir, 'js'));
}

/**
 * Declare a JavaScript string to be used on the page and included in
 * the page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @since 1.5
 * @see head_js()
 * @param string $string JavaScript string to include.
 */
function queue_js_string($string)
{
    get_view()->headScript()->appendScript($string);
}

/**
 * Declare that a CSS file or files will be used on the page.
 * All "used" stylesheets will be included in the page's head.
 *
 * This needs to be called either before head(), or in a plugin_header hook.
 *
 * @since 1.3
 * @see head_css()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $media CSS media declaration, defaults to 'all'.
 * @param string|bool $conditional Optional IE-style conditional comment, used
 *  generally to include IE-specific styles. Defaults to false.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 * @return void
 */
function queue_css_file($file, $media = 'all', $conditional = false, $dir = 'css')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_css_file($singleFile, $media, $conditional, $dir);
        }
        return;
    }
    get_view()->headLink()->appendStylesheet(css_src($file, $dir), $media, $conditional);
}

/**
 * Declare a CSS string to be used on the page and included in the
 * page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @since 1.5
 * @see head_css
 * @param string $string CSS string to include.
 * @param string $media CSS media declaration, defaults to 'all'.
 * @param string|bool $conditional Optional IE-style conditional comment,
 *  used generally to include IE-specific styles. Defaults to false.
 */
function queue_css_string($string, $media = 'all', $conditional = false)
{
    $attrs = array();
    if ($media) {
        $attrs['media'] = $media;
    }
    if ($conditional) {
        $attrs['conditional'] = $conditional;
    }
    get_view()->headStyle()->appendStyle($string, $attrs);
}

/**
 * Print the JavaScript tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @since 1.3
 * @see queue_js_file()
 * @param bool $includeDefaults Whether the default javascripts should be
 *  included. Defaults to true.
 * @return void
 */
function head_js($includeDefaults = true)
{
    $headScript = get_view()->headScript();

    if ($includeDefaults) {
        $dir = 'javascripts';
        $config = Zend_Registry::get('bootstrap')->getResource('Config');
        $useInternalJs = isset($config->theme->useInternalJavascripts)
                ? (bool) $config->theme->useInternalJavascripts
                : false;

        $headScript->prependScript('jQuery.noConflict();');
        if ($useInternalJs) {
            $headScript->prependFile(src('jquery-ui', $dir, 'js'))
                       ->prependFile(src('jquery', $dir, 'js'));
        } else {
            $headScript->prependFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js')
                       ->prependFile('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
        }
    }

    return $headScript;
}

/**
 * Print the CSS link tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @since 1.3
 * @see queue_css_file()
 * @return void
 */
function head_css()
{
    return get_view()->headLink() . get_view()->headStyle();
}

/**
 * Retrieve the web path to a css file.
 *
 * @param string $file Should not include the .css extension
 * @param string $dir Defaults to 'css'
 * @return string
 */
function css_src($file, $dir = 'css')
{
    return src($file, $dir, 'css');
}

/**
 * Retrieve the web path to an image file.
 *
 * @since 0.9
 * @param string $file Filename, including the extension.
 * @param string $dir Optional Directory within the theme to look for image
 * files.  Defaults to 'images'.
 * @return string
 */
function img($file, $dir = 'images')
{
    return src($file, $dir);
}

/**
 * Echos the web path (that's what's important to the browser)
 * to a javascript file.
 * $dir defaults to 'javascripts'
 * $file should not include the .js extension
 *
 * @param string $file The name of the file, without .js extension.
 * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
 */
function js_src($file, $dir = 'javascripts')
{
    $href = src($file, $dir, 'js');

    return '<script type="text/javascript" src="' . html_escape($href) . '" charset="utf-8"></script>'."\n";
}

/**
 * Return a valid src attribute value for a given file.  Used primarily
 * by other helper functions.
 *
 *
 * @param string        Filename
 * @param string|null   Directory that the file is contained in (optional)
 * @param string        File extension (optional)
 * @return string
 */
function src($file, $dir=null, $ext = null)
{
    if ($ext !== null) {
        $file .= '.'.$ext;
    }
    if ($dir !== null) {
        $file = $dir. '/' .$file;
    }
    return web_path_to($file);
}

/**
 * Return the physical path for an asset/resource within the theme (or plugins, shared, etc.)
 *
 * @param string $file
 * @throws Exception
 * @return string
 */
function physical_path_to($file)
{
    $view = get_view();
    $paths = $view->getAssetPaths();

    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $physical . '/' . $file;
        }
    }
    throw new InvalidArgumentException( __("Could not find file %s!",$file) );
}

/**
 * Return the web path for an asset/resource within the theme
 *
 * @param string $file
 * @return string
 */
function web_path_to($file)
{
    $view = get_view();
    $paths = $view->getAssetPaths();
    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $web . '/' . $file;
        }
    }
    throw new InvalidArgumentException( __("Could not find file %s!",$file) );
}
