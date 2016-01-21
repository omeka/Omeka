<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * View Helper for displaying files through Omeka.
 * 
 * This will determine how to display any given file based on the MIME type 
 * (Internet media type) of that file. Individual rendering agents are defined 
 * by callbacks that are either contained within this class or defined by 
 * plugins. Callbacks defined by plugins will override native class methods if 
 * defined for existing MIME types. In order to define a rendering callback that 
 * should be in the core of Omeka, define a method in this class and then make 
 * sure that it responds to all the correct MIME types by modifying other 
 * properties in this class.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_FileMarkup extends Zend_View_Helper_Abstract
{   
    /**
     * Array of MIME types and the callbacks that can process it.
     *
     * Example:
     * array('video/avi'=>'wmv');
     *
     * @var array
     */
    static protected $_callbacks = array(
        'audio/ogg'         => 'audio',
        'audio/x-ogg'       => 'audio',
        'audio/aac'         => 'audio',
        'audio/x-aac'       => 'audio',
        'audio/aiff'        => 'audio',
        'audio/x-aiff'      => 'audio',
        'audio/mp3'         => 'audio',
        'audio/mpeg'        => 'audio',
        'audio/mpeg3'       => 'audio',
        'audio/mpegaudio'   => 'audio',
        'audio/mpg'         => 'audio',
        'audio/x-mp3'       => 'audio',
        'audio/x-mpeg'      => 'audio',
        'audio/x-mpeg3'     => 'audio',
        'audio/x-mpegaudio' => 'audio',
        'audio/x-mpg'       => 'audio',
        'audio/mp4'         => 'audio',
        'audio/x-mp4'       => 'audio',
        'audio/x-m4a'       => 'audio',
        'audio/wav'         => 'audio',
        'audio/x-wav'       => 'audio',
        'video/mp4'         => 'video',
        'video/x-m4v'       => 'video',
        'video/ogg'         => 'video',
        'video/webm'        => 'video',
        'video/quicktime'   => 'video',
    );
    
    /**
     * Array of file extensions and the callbacks that can process them.
     * 
     * Taken from http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     * 
     * @var array
     */
    static private $_fileExtensionCallbacks = array(
        // application/ogg
        'ogx' => 'audio',
        // audio/x-aac
        'aac' => 'audio',
        // audio/x-aiff
        'aif' => 'audio',
        'aiff' => 'audio',
        'aifc' => 'audio',
        // audio/mpeg
        'mpga' => 'audio',
        'mp2' => 'audio',
        'mp2a' => 'audio',
        'mp3' => 'audio',
        'm2a' => 'audio',
        'm3a' => 'audio',
        // audio/mp4
        'mp4a' => 'audio',
        'm4a' => 'audio',
        // audio/ogg
        'oga' => 'audio',
        'ogg' => 'audio',
        'spx' => 'audio',
        'opus' => 'audio',
        // audio/x-wav
        'wav' => 'audio',
        // video/mp4
        'mp4' => 'video',
        'mp4v' => 'video',
        'mpg4'  => 'video',
        'm4v' => 'video',
        // video/ogg
        'ogv' => 'video',
        // video/webm
        'webm' => 'video',
        // video/quicktime
        'mov' => 'video',
    );
    
    /**
     * The array consists of the default options which are passed to the 
     * callback.
     *
     * @var array
     */
    static protected $_callbackOptions = array(
        'defaultDisplay'=>array(
            'linkToFile'=>true,
            'linkToMetadata'=>false,
            'linkText' => null, 
            ),
        'derivativeImage'=>array(
            'imageSize'=>'square_thumbnail',
            'linkToFile'=>true,
            'linkToMetadata'=>false,
            'imgAttributes' => array()
            ),
        'video'=>array(
            'width' => '320',
            'height' => '240',
            'autoplay' => false,
            'controller'=> true,
            'loop'=> false,
            ),
        'audio' => array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
        ),
        'icon'=>array(
            'showFilename' => true,
            'icons' => array(),
            'linkToFile' => true,
            'linkToMetadata' => false,
            'linkAttributes' => array(),
            'imgAttributes' => array(),
            'filenameAttributes' => array()
            )
        );

    /**
     * Images to show when a file has no derivative.
     *
     * @var array
     */
    static protected $_fallbackImages = array(
        'audio' => 'fallback-audio.png',
        'image' => 'fallback-image.png',
        'video' => 'fallback-video.png',
    );

    /**
     * Fallback image used when no other fallbacks are appropriate.
     *
     * @var string
     */
    const GENERIC_FALLBACK_IMAGE = 'fallback-file.png';

    /**
     * Add MIME types and/or file extensions and associated callbacks to the 
     * list.
     * 
     * This allows plugins to override/define ways of displaying specific files. 
     * The most obvious example of where this would come in handy is to define 
     * ways of displaying uncommon files, such as QTVR, or novel ways of 
     * displaying more common files, such as using iPaper to display PDFs.
     *
     * @see add_mime_display_type()
     * @internal This method (and the properties upon which it operates) are 
     * static because it gets called prior to instantiation of the view, i.e.
     * in the plugin loading phase.  Since there is no way to inject view
     * helpers into the view object, this helper object cannot be instantiated
     * and registered for use by the add_mime_display_type() function.
     * 
     * @param array|string $fileIdentifiers Set of MIME types (Internet media 
     * types) and/or file extensions that this specific callback will respond 
     * to. Accepts the following:
     * <ul>
     *     <li>A string containing one MIME type: 
     *     <code>'application/msword'</code></li>
     *     <li>A simple array containing MIME types: 
     *     <code>array('application/msword', 'application/doc')</code></li>
     *     <li>A keyed array containing MIME types: 
     *     <code>array('mimeTypes' => array('application/msword', 'application/doc'))</code></li>
     *     <li>A keyed array containing file extensions: 
     *     <code>array('fileExtensions' => array('doc', 'docx''DOC', 'DOCX'))</code></li>
     *     <li>A keyed array containing MIME types and file extensions: <code>
     *     array(
     *         'mimeTypes' => array(
     *             'application/msword', 
     *             'application/doc', 
     *             'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
     *         ), 
     *         'fileExtensions' => array('doc', 'docx', 'DOC', 'DOCX'), 
     *     )
     *     </code></li>
     * </ul>
     * Note that file extensions are case sensitive.
     * @param callback Any valid callback.  This function should return a string 
     * containing valid XHTML, which will be used to display the file.
     * @param array $defaultOptions
     * @param array $fileExtensions
     * @return void
     */
    public static function addMimeTypes($fileIdentifiers, $callback, array $defaultOptions = array())
    {
        // Create the keyed list of mimeType => callback and fileExtension => 
        // callback format, and merge them with the current lists.
        $callbackListMimeTypes = array();
        $callbackListFileExtensions = array();
        
        // Interpret string as MIME type.
        if (is_string($fileIdentifiers)) {
            $fileIdentifiers = (array) $fileIdentifiers;
            $fillArray = array_fill(0, count($fileIdentifiers), $callback);
            $callbackListMimeTypes = array_combine($fileIdentifiers, $fillArray);
        } else if (is_array($fileIdentifiers)) {
            // Interpret unkeyed array as MIME types.
            if (array_key_exists(0, $fileIdentifiers)) {
                $fillArray = array_fill(0, count($fileIdentifiers), $callback);
                $callbackListMimeTypes = array_combine($fileIdentifiers, $fillArray);
            // Interpret keyed array as MIME types and/or file extensions.
            } else {
                if (array_key_exists('mimeTypes', $fileIdentifiers)) {
                    $fillArray = array_fill(0, count($fileIdentifiers['mimeTypes']), $callback);
                    $callbackListMimeTypes = array_combine($fileIdentifiers['mimeTypes'], $fillArray);
                }
                if (array_key_exists('fileExtensions', $fileIdentifiers)) {
                    $fillArray = array_fill(0, count($fileIdentifiers['fileExtensions']), $callback);
                    $callbackListFileExtensions = array_combine($fileIdentifiers['fileExtensions'], $fillArray);
                }
            }
        }
        
        self::$_callbacks = array_merge(self::$_callbacks, $callbackListMimeTypes);
        self::$_fileExtensionCallbacks = array_merge(self::$_fileExtensionCallbacks, $callbackListFileExtensions);
        
        // Add this callback's default options to the list.
        $key = self::_getCallbackKey($callback);
        self::$_callbackOptions[$key] = $defaultOptions;
    }

    /**
     * Add a fallback image for the given mime type or type family.
     *
     * @param string $mimeType The mime type this fallback is for, or the mime
     *  "prefix" it is for (video, audio, etc.)
     * @param string $image The name of the image to use, as would be passed to
     *  img()
     */
    public static function addFallbackImage($mimeType, $image)
    {
        self::$_fallbackImages[$mimeType] = $image;
    }
    
    /**
     * Default display for MIME types that do not have a valid rendering 
     * callback.  
     *
     * This wraps the original filename in a link to download that file, with a 
     * class of "download-file".  Any behavior more complex than that should be 
     * processed with a valid callback.
     * 
     * @param File $file
     * @param array $options
     * @return string HTML
     */
    public function defaultDisplay($file, array $options=array())
    {
        if ($options['linkText']) {
            $html = $options['linkText'];
        }
        return $this->_linkToFile($file, $options);
    }
        
    /**
     * Add a link for the file based on the given set of options.
     * 
     * If the 'linkToMetadata' option is true, then link to the file
     * metadata page (files/show).  If 'linkToFile' is true,
     * link to the original file, and if 'linkToFile' is a string, try
     * to link to that specific derivative. Otherwise just return the
     * $html without wrapping in a link.
     * 
     * The attributes for the link will be based off the 'linkAttributes' 
     * option, which should be an array.
     * 
     * If $html is null, it defaults to original filename of the file.
     * 
     * @param File $file
     * @param array $options
     * @param string $html
     * @return string
     */
    protected function _linkToFile($file, $options, $html = null)
    {
        if ($html === null) {
            $fileTitle = strip_formatting(metadata($file, array('Dublin Core', 'Title')));
            $html = $fileTitle ? $fileTitle : metadata($file, 'Original Filename');
        }

        $linkAttributes = isset($options['linkAttributes'])
                        ? $options['linkAttributes']
                        : array();

        if ($options['linkToMetadata']) {
            $html = link_to_file_show($linkAttributes, $html, $file);
        } else if (($linkToFile = $options['linkToFile'])) {
            // If you've manually specified a derivative type to link
            // to, and this file actually has derivatives, we'll use
            // that, otherwise, the link is to the "original" file.
            if (is_string($linkToFile) && $file->hasThumbnail()) {
                $derivative = $linkToFile;
            } else {
                $derivative = 'original';
            }

            // Wrap in a link that will download the file directly.
            $defaultLinkAttributes = array(
                'class'=>'download-file', 
                'href'=>$file->getWebPath($derivative)
                );
            $linkAttributes = array_merge($defaultLinkAttributes, $linkAttributes);
            $html = '<a ' . tag_attributes($linkAttributes) . '>' . $html . '</a>';
        }
        return $html;
    }
        
    /**
     * Retrieve valid XHTML for displaying Quicktime video files
     * 
     * @param File $file
     * @param array $options The set of default options for this includes:
     *  width, height, autoplay, controller, loop
     * @return string
     */ 
    public function video($file, array $options=array())
    {
        return $this->_media('video', $file, $options);
    }
    
    /**
     * Default display of audio files via <audio> tag.
     * 
     * @param File $file
     * @param array $options The set of default options for this includes:
     *  width, height, autoplay, controller, loop
     * @return string
     */
    public function audio($file, array $options)
    {
        return $this->_media('audio', $file, $options);
    }

    protected function _media($type, $file, array $options)
    {
        if ($type !== 'audio' && $type !== 'video') {
            $type = 'video';
        }
        $url = $file->getWebPath('original');
        $escapedUrl = html_escape($url);
        $attrs = array(
            'src' => $url,
            'class' => 'omeka-media',
            'width' => $options['width'],
            'height' => $options['height'],
            'controls' => (bool) $options['controller'],
            'autoplay' => (bool) $options['autoplay'],
            'loop'     => (bool) $options['loop'],
        );
        $html = '<' . $type . ' ' . tag_attributes($attrs) . '>'
            . '<a href="' . $escapedUrl . '">' . metadata($file, 'display_title') . '</a>'
            . '</audio>';
        return $html;
    }
    
    /**
     * Default display of an icon to represent a file.
     * 
     * Example usage:
     * 
     * echo files_for_item(array(
     *            'showFilename'=>false,
     *            'linkToFile'=>false,
     *            'linkAttributes'=>array('rel'=>'lightbox'),
     *            'filenameAttributes'=>array('class'=>'error'),
     *            'imgAttributes'=>array('id'=>'foobar'),
     *            'icons' => array('audio/mpeg'=>img('audio.gif'))));
     * 
     * @param File
     * @param array $options Available options include: 
     *      'showFilename' => boolean, 
     *      'linkToFile' => boolean,
     *      'linkAttributes' => array, 
     *      'filenameAttributes' => array (for the filename div), 
     *      'imgAttributes' => array, 
     *      'icons' => array.
     * @return string
     */
    public function icon($file, array $options=array())
    {
        $mimeType = $file->mime_type;
        $imgAttributes = (array)$options['imgAttributes'];
        // The path to the icon is keyed to the MIME type of the file.
        $imgAttributes['src'] = (string)$options['icons'][$mimeType];
        
        $html = '<img ' . tag_attributes($imgAttributes) . ' />';
        
        if ($options['showFilename']) {
            // Add a div with arbitrary attributes.
            $html .= '<div ' . tag_attributes((array)$options['filenameAttributes']) 
                   . '>' . html_escape($file->original_filename) . '</div>';
        }
        
        return $this->_linkToFile($file, $options, $html);
    }
    
    
    /**
     * Returns valid XHTML markup for displaying an image that has been stored 
     * in Omeka.
     * 
     * @param File $file
     * @param array $file Options for customizing the display of images. Current
     * options include: 'imageSize'
     * @return string HTML for display
     */
    public function derivativeImage($file, array $options=array())
    {
        $html = '';
        $imgHtml = '';
        
        // Should we ever include more image sizes by default, this will be 
        // easier to modify.        
        $imgClasses = array(
            'thumbnail'=>'thumb', 
            'square_thumbnail'=>'thumb', 
            'fullsize'=>'full');
        $imageSize = $options['imageSize'];
        
        // If we can make an image from the given image size.
        if (in_array($imageSize, array_keys($imgClasses))) {
            
            // A class is given to all of the images by default to make it 
            // easier to style. This can be modified by passing it in as an 
            // option, but recommended against. Can also modify alt text via an 
            // option.
            $imgClass = $imgClasses[$imageSize];
            $imgAttributes = array_merge(array('class' => $imgClass),
                                (array)$options['imgAttributes']);
            $imgHtml = $this->image_tag($file, $imgAttributes, $imageSize);
        }
        $html .= !empty($imgHtml) ? $imgHtml : html_escape($file->original_filename);   
        $html = $this->_linkToFile($file, $options, $html);
        return $html;
    }
    // END DEFINED DISPLAY CALLBACKS
    
    protected function getCallback($file, $options)
    {
        $mimeType = $file->mime_type;
        $fileExtension = $file->getExtension();
        
        // Displaying icons overrides the default lookup mechanism.
        if (array_key_exists('icons', $options) and
                array_key_exists($mimeType, $options['icons'])) {
            return 'icon';
        }
        
        if (array_key_exists($mimeType, self::$_callbacks)) {
            $name = self::$_callbacks[$mimeType];
        } else if (array_key_exists($fileExtension, self::$_fileExtensionCallbacks)) {
            $name = self::$_fileExtensionCallbacks[$fileExtension];
        } else if ($file->hasThumbnail()) {
            $name = 'derivativeImage';
        } else {
            $name = 'defaultDisplay';
        }
        
        return $name;
    }
    
    /**
     * @see Omeka_Plugin_Broker::addMediaAdapter()
     * @param mixed $callback
     * @return array
     */
    protected function getDefaultOptions($callback)
    {
        $key = self::_getCallbackKey($callback);
        if (array_key_exists($key, self::$_callbackOptions)) {
            return (array) self::$_callbackOptions[$key];
        } else {
            return array();
        }
    }
    
    /**
     * Retrieve the HTML for a given file from the callback.   
     * 
     * @param File $file
     * @param callback $renderer Any valid callback that will display the HTML.
     * @param array $options Set of options passed to the rendering callback.
     * @return string HTML for displaying the file.
     */
    protected function getHtml($file, $renderer, array $options)
    {
        //Format the callback based on whether we can actually run it
        
        //If the callback is native to this object, get it valid and run it
        if(is_string($renderer) and method_exists($this, $renderer)) {
            $renderer = array($this, $renderer);
        }
        
        return call_user_func_array($renderer, array($file, $options));
    }
    
    /**
     * Bootstrap for the helper class.  This will retrieve the HTML for
     * displaying the file and by default wrap it in a <div class="item-file">.
     * 
     * @param File $file
     * @param array $props Set of options passed by a theme writer to the
     * customize the display of any given callback.
     * @param array $wrapperAttributes
     * @return string HTML
     */
    public function fileMarkup($file, array $props=array(), $wrapperAttributes = array())
    {        
        // There is a chance that $props passed in could modify the callback
        // that is used.  Currently used to determine whether or not to display
        // an icon.
        $callback = $this->getCallback($file, $props);   
        
        $options = array_merge($this->getDefaultOptions($callback), $props);
        
        $html  = $this->getHtml($file, $callback, $options);
        
        // Append a class name that corresponds to the MIME type.
        if ($wrapperAttributes) {
            $mimeTypeClassName = str_ireplace('/', '-', $file->mime_type);
            if (array_key_exists('class', $wrapperAttributes)) {
                $wrapperAttributes['class'] .= ' ' . $mimeTypeClassName;
            } else {
                $wrapperAttributes['class']  = $mimeTypeClassName;
            }
        }
        
        //Wrap the HTML in a div with a class (if class is not set to null)
        $wrapper = !empty($wrapperAttributes) ? '<div ' . tag_attributes($wrapperAttributes) . '>' : ''; 
        $html = !empty($wrapper) ? $wrapper . $html . "</div>" : $html;
        
        return apply_filters(
            'file_markup', 
            $html, 
            array(
                'file' => $file, 
                'callback' => $callback, 
                'options' => $options, 
                'wrapper_attributes' => $wrapperAttributes, 
            )
        );
    }
        
    /**
     * Return a valid img tag for an image.
     *
     * @param Omeka_Record_AbstractRecord $record
     * @param array $props Image tag attributes
     * @param string $format Derivative image type (thumbnail, etc.)
     * @return string
     */
    public function image_tag($record, $props, $format)
    {
        if (!($record && $record instanceof Omeka_Record_AbstractRecord)) {
            return false;
        }

        // Use the default representative file.
        $file = $record->getFile();
        if (!$file) {
            return false;
        }

        if ($file->hasThumbnail()) {
            $uri = $file->getWebPath($format);
        } else {
            $uri = img($this->_getFallbackImage($file));
        }
        $props['src'] = $uri;

        /** 
         * Determine alt attribute for images
         * Should use the following in this order:
         * 1. passed 'alt' prop
         * 2. first Dublin Core Title for $file
         * 3. original filename for $file
         */
        $alt = '';
        if (isset($props['alt'])) {
            $alt = $props['alt'];
        } else if ($fileTitle = metadata($file, 'display title', array('no_escape' => true))) {
            $alt = $fileTitle;
        }
        $props['alt'] = $alt;
        
        $title = '';
        if (isset($props['title'])) {
            $title = $props['title'];
        } else {
            $title = $alt;
        }
        $props['title'] = $title;
        
        // Build the img tag
        return '<img ' . tag_attributes($props) . '>';
    }

    /**
     * Get the name of a fallback image to use for this file.
     *
     * The fallback used depends on the file's mime type.
     *
     * @see self::addFallbackImage()
     * @param File $file The file to get a fallback for.
     * @return string Name of the image to use.
     */
    protected function _getFallbackImage($file)
    {
        $mimeType = $file->mime_type;
        if (isset(self::$_fallbackImages[$mimeType])) {
            return self::$_fallbackImages[$mimeType];
        }

        $mimePrefix = substr($mimeType, 0, strpos($mimeType, '/'));
        if (isset(self::$_fallbackImages[$mimePrefix])) {
            return self::$_fallbackImages[$mimePrefix];
        }

        return self::GENERIC_FALLBACK_IMAGE;
    }

    /**
     * Get a string key to represent a given callback.
     *
     * This key can be used to store and retrieve data about the
     * callback, like default options.
     *
     * @param callback $callback
     * @return string
     */
    protected static function _getCallbackKey($callback)
    {
        if (is_string($callback)) {
            return $callback;
        } else if (is_callable($callback, false, $name)) {
            return $name;
        } else {
            throw new InvalidArgumentException('Invalid file display callback.');
        }
    }
}
