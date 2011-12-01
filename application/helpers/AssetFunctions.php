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
 * @see display_js()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 * @return void
 */
function queue_js($file, $dir = 'javascripts')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_js($singleFile, $dir);
        }
        return;
    }
    __v()->headScript()->appendFile(src($file, $dir, 'js'));
}

/**
 * Declare a JavaScript string to be used on the page and included in
 * the page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @since 1.5
 * @see display_js()
 * @param string $string JavaScript string to include.
 */
function queue_js_string($string)
{
    __v()->headScript()->appendScript($string);
}

/**
 * Declare that a CSS file or files will be used on the page.
 * All "used" stylesheets will be included in the page's head.
 *
 * This needs to be called either before head(), or in a plugin_header hook.
 *
 * @since 1.3
 * @see display_css()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $media CSS media declaration, defaults to 'all'.
 * @param string|bool $conditional Optional IE-style conditional comment, used
 *  generally to include IE-specific styles. Defaults to false.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 * @return void
 */
function queue_css($file, $media = 'all', $conditional = false, $dir = 'css')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_css($singleFile, $media, $conditional, $dir);
        }
        return;
    }
    __v()->headLink()->appendStylesheet(css($file, $dir), $media, $conditional);
}

/**
 * Declare a CSS string to be used on the page and included in the
 * page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @since 1.5
 * @see display_css
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
    __v()->headStyle()->appendStyle($string, $attrs);
}

/**
 * Print the JavaScript tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @since 1.3
 * @see queue_js()
 * @param bool $includeDefaults Whether the default javascripts should be
 *  included. Defaults to true.
 * @return void
 */
function display_js($includeDefaults = true)
{
    $headScript = __v()->headScript();

    if ($includeDefaults) {
        $dir = 'javascripts';
        $config = Omeka_Context::getInstance()->getConfig('basic');
        $useInternalJs = isset($config->theme->useInternalJavascripts)
                ? (bool) $config->theme->useInternalJavascripts
                : false;

        // For backwards compatibility: include Prototype and friends only
        // when specifically requested in the admin interface.
        if (get_option('enable_prototype') == '1') {
            $headScript->prependFile(src('scriptaculous', $dir, 'js').'?load=effects,dragdrop')
                       ->prependFile(src('prototype-extensions', $dir, 'js'))
                       ->prependFile(src('prototype', $dir, 'js'));
        }

        $headScript->prependScript('jQuery.noConflict();');
        if ($useInternalJs) {
            $headScript->prependFile(src('jquery-ui', $dir, 'js'))
                       ->prependFile(src('jquery', $dir, 'js'));
        } else {
            $headScript->prependFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js')
                       ->prependFile('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
        }
    }

    echo $headScript;
}

/**
 * Print the CSS link tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @since 1.3
 * @see queue_css()
 * @return void
 */
function display_css()
{
    echo __v()->headLink();
    echo __v()->headStyle();
}

/**
 * Retrieve the web path to a css file.
 *
 * @param string $file Should not include the .css extension
 * @param string $dir Defaults to 'css'
 * @return string
 */
function css($file, $dir = 'css')
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
 * @param string $file The name of the file, without .js extension.  Specifying 'default' will load
 * the default javascript files, such as prototype/scriptaculous
 * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
 * @param array $scriptaculousLibraries An array of Scriptaculous libraries, by file name. Default is 'effects' and 'dragdrop'. Works only if 'default' is passed for the first parameter.
 */
function js($file, $dir = 'javascripts', $scriptaculousLibraries = array('effects', 'dragdrop'))
{
    if ($file == 'default') {
        $output = '';
        // For backwards compatibility: include Prototype and friends only
        // when specifically requested in the admin interface.
        if (get_option('enable_prototype') == '1') {
            $output .= js('prototype', $dir);
            $output .= js('prototype-extensions', $dir);
            $output .= js('scriptaculous', $dir, $scriptaculousLibraries);
        }
        $output .= js('jquery', $dir);
        $output .= js('jquery-noconflict', $dir);
        $output .= js('jquery-ui', $dir);

        //Do not try to load 'default.js'
        return $output;
    }

    if ('scriptaculous' == $file) {
        $href = src($file, $dir, 'js') . ($scriptaculousLibraries ? '?load=' . implode(',', $scriptaculousLibraries) : '');
    } else {
        $href = src($file, $dir, 'js');
    }

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
    $view = __v();
    $paths = $view->getAssetPaths();

    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $physical . '/' . $file;
        }
    }
    throw new Exception( __("Could not find file %s!",$file) );
}

/**
 * Return the web path for an asset/resource within the theme
 *
 * @param string $file
 * @return string
 */
function web_path_to($file)
{
    $view = __v();
    $paths = $view->getAssetPaths();
    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $web . '/' . $file;
        }
    }
    throw new Exception( __("Could not find file %s!",$file) );
}
