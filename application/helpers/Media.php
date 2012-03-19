<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
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
 *
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_View_Helper_Media
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
        'application/ogg'   => 'ogg',
        'audio/ogg'         => 'ogg',
        'audio/x-ogg'       => 'ogg',
        'audio/aac'         => 'aac',
        'audio/x-aac'       => 'aac',
        'audio/aiff'        => 'aiff',
        'audio/x-aiff'      => 'aiff',
        'audio/midi'        => 'midi',
        'audio/x-midi'      => 'midi',
        'audio/mp3'         => 'mp3',
        'audio/mpeg'        => 'mp3',
        'audio/mpeg3'       => 'mp3',
        'audio/mpegaudio'   => 'mp3',
        'audio/mpg'         => 'mp3',
        'audio/x-mp3'       => 'mp3',
        'audio/x-mpeg'      => 'mp3',
        'audio/x-mpeg3'     => 'mp3',
        'audio/x-mpegaudio' => 'mp3',
        'audio/x-mpg'       => 'mp3',
        'audio/mp4'         => 'mp4',
        'audio/x-mp4'       => 'mp4',
        'audio/wav'         => 'wav',
        'audio/x-wav'       => 'wav',
        'image/bmp'         => 'image',
        'image/gif'         => 'image',
        'image/jpeg'        => 'image',
        'image/jpg'         => 'image',
        'image/pjpeg'       => 'image',
        'image/png'         => 'image',
        'image/tif'         => 'image',
        'image/tiff'        => 'image', 
        'image/x-ms-bmp'    => 'image',
        'video/mp4'         => 'mov',
        'video/mpeg'        => 'mov',
        'video/ogg'         => 'mov',
        'video/quicktime'   => 'mov',
        'audio/wma'         => 'wma',
        'audio/x-ms-wma'    => 'wma',
        'video/avi'         => 'wmv',
        'video/msvideo'     => 'wmv',
        'video/x-msvideo'   => 'wmv',
        'video/x-ms-wmv'    => 'wmv', 
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
        'ogx' => 'ogg', 
        // audio/x-aac
        'aac' => 'aac', 
        // audio/x-aiff
        'aif' => 'aiff', 
        'aiff' => 'aiff', 
        'aifc' => 'aiff', 
        // audio/midi
        'mid' => 'midi', 
        'midi' => 'midi', 
        'kar' => 'midi', 
        'rmi' => 'midi', 
        // audio/mpeg
        'mpga' => 'mp3', 
        'mp2' => 'mp3', 
        'mp2a' => 'mp3', 
        'mp3' => 'mp3', 
        'm2a' => 'mp3', 
        'm3a' => 'mp3', 
        // audio/mp4
        'mp4a' => 'mp4', 
        // audio/ogg
        'oga' => 'ogg', 
        'ogg' => 'ogg', 
        'spx' => 'ogg', 
        // audio/x-wav
        'wav' => 'wav', 
        // image/bmp
        'bmp' => 'image', 
        // image/gif
        'gif' => 'image', 
        // image/jpeg
        'jpeg' => 'image', 
        'jpg' => 'image', 
        'jpe' => 'image', 
        // image/png
        'png' => 'image', 
        // image/tiff
        'tif' => 'image', 
        'tiff' => 'image', 
        // video/mp4
        'mp4' => 'mov', 
        'mp4v' => 'mov',  
        'mpg4'  => 'mov', 
        // video/mpeg
        'mpeg' => 'mov', 
        'mpg' => 'mov', 
        'mpe' => 'mov', 
        'm1v' => 'mov', 
        'm2v'  => 'mov', 
        // video/ogg
        'ogv' => 'mov', 
        // video/quicktime
        'qt' => 'mov', 
        'mov' => 'mov', 
        // audio/x-ms-wma
        'wma' => 'wma', 
        // video/x-msvideo
        'avi' => 'wmv', 
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
        'image'=>array(
            'imageSize'=>'square_thumbnail',
            'linkToFile'=>true,
            'linkToMetadata'=>false,
            'imgAttributes' => array()
            ),
        'wmv'=>array(
            'width' => '320',
            'height' => '240', 
            'autostart' => 0,
            'ShowControls'=> 1,
            'ShowDisplay'=> 0,
            'ShowStatusBar' => 0,
            'scale' => 'aspect'
            ),
        'wma'=>array(
            'width' => '320',
            'height' => '46',
            'autostart' => 0,
            'ShowControls'=> 1,
            'ShowDisplay'=> 0,
            'ShowStatusBar' => 0
            ),
        'mov'=>array(
            'width' => '320',
            'height' => '240',
            'autoplay' => false,
            'controller'=> true,
            'loop'=> false,
            'scale' => 'aspect'
            ),
        'ogg'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'mp3'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'aac'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'aiff'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'midi'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'mp4'=>array(
            'width' => '200',
            'height' => '20',
            'autoplay' => false,
            'controller' => true,
            'loop' => false
            ),
        'wav'=>array(
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
        
        // Create the keyed list of callback=>options format, and add it to the 
        // current list
        
        //The key for the array might be the serialized callback (if necessary)
        $callbackKey = !is_string($callback) ? serialize($callback) : $callback;
        self::$_callbackOptions[$callbackKey] = $defaultOptions;      
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
        $html = null;
        if ($options['linkText']) {
            $html = $options['linkText'];
        }
        return $this->_linkToFile($html, $file, $options);
    }
    
    /**
     * Add a link for the file based on the given set of options.
     * 
     * If the 'linkToMetadata' option is true, then link to the file metadata
     * page (files/show).  Otherwise if 'linkToFile' is true, link to download
     * the file.  Otherwise just return the $html without wrapping in a link.
     * 
     * The attributes for the link will be based off the 'linkAttributes' 
     * option, which should be an array.
     * 
     * If $html is null, it defaults to original filename of the file.
     * 
     * @param string $html
     * @param File $file
     * @param array $options
     * @return string
     */
    protected function _linkToFile($html = null, $file, $options)
    {
        if ($html === null) {
            $html = item_file('Original Filename', null, array(), $file);
        }
        if ($options['linkToMetadata']) {
          $html = link_to_file_metadata((array)$options['linkAttributes'], 
                  $html, $file);
        } else if ($options['linkToFile']) {
            // Wrap in a link that will download the file directly.
            $defaultLinkAttributes = array(
                'class'=>'download-file', 
                'href'=>file_download_uri($file)
                );
            $linkAttributes = array_key_exists('linkAttributes', $options)
                            ? $options['linkAttributes'] : array();
            $linkAttributes = array_merge($defaultLinkAttributes, $linkAttributes);
            $html = '<a ' . _tag_attributes($linkAttributes) . '>' . $html . '</a>';
        }
        return $html;
    }
    
    /**
     * Returns valid XHTML markup for displaying an image that has been archived 
     * through Omeka.  
     * 
     * @param File $file
     * @param array $file Options for customizing the display of images. Current
     * options include: 'imageSize'
     * @return string HTML for display
     */
    public function image($file, array $options=array())
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
                                
            // Luckily, helper function names correspond to the name of the 
            // 'imageSize' option.
            $imgHtml = $this->$imageSize($file, $imgAttributes);
        }
        $html .= !empty($imgHtml) ? $imgHtml : html_escape($file->original_filename);   
        $html = $this->_linkToFile($html, $file, $options);
        return $html;
    }
    
    /**
     * Retrieve valid XHTML for displaying a wmv video file or equivalent.  
     * Currently this loads the video inside of an <object> tag, but that 
     * provides less flexibility than a flash wrapper, which seems to be a 
     * standard Web2.0 practice for video sharing.  This limitation can be
     * overcome by a plugin that used a flash wrapper for displaying video.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */ 
    public function wmv($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));
        $html = '<object type="application/x-mplayer2" width="'.$options['width'].'" height="'.$options['height'].'" data="'.$path.'" autoStart="'.$options['autostart'].'">'
              . '<param name="FileName" value="'.$path.'" />'
              . '<param name="autoStart" value="'.($options['autostart'] ? 'true' : 'false').'" />'
              . '<param name="ShowAudioControls" value="'.($options['ShowControls'] ? 'true' : 'false').'" />'
              . '<param name="ShowStatusBar" value="'.($options['ShowStatusBar'] ? 'true' : 'false').'" />'
              . '<param name="ShowDisplay" value="'.($options['ShowDisplay'] ? 'true' : 'false').'" />'
              // This param is for QuickTime clients
              . '<param name="scale" value="' . $options['scale'] . '" />'
              . '</object>';
        return $html;
    }
    
    /**
     * Retrieve valid XHTML for displaying a wma audio file or equivalent.  
     * Currently this loads the video inside of an <object> tag, but that
     * provides less flexibility than a flash wrapper, which seems to be a 
     * standard Web2.0 practice for video sharing.  This limitation can be
     * overcome by a plugin that used a flash wrapper for displaying video.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */ 
    public function wma($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));
        $html = '<object type="audio/x-ms-wma" width="'.$options['width'].'" height="'.$options['height'].'" data="'.$path.'" autoStart="'.$options['autostart'].'">'
              . '<param name="FileName" value="'.$path.'" />'
              . '<param name="autoStart" value="'.($options['autostart'] ? 'true' : 'false').'" />'
              . '<param name="ShowControls" value="'.($options['ShowControls'] ? 'true' : 'false').'" />'
              . '<param name="ShowStatusBar" value="'.($options['ShowStatusBar'] ? 'true' : 'false').'" />'
              . '<param name="ShowDisplay" value="'.($options['ShowDisplay'] ? 'true' : 'false').'" />'
              . '</object>';
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
    public function mov($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));
        $html = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$options['width'].'" height="'.$options['height'].'">'
              . '<param name="src" value="'.$path.'" />'
              . '<param name="controller" value="'.($options['controller'] ? 'true' : 'false').'" />'
              . '<param name="autoplay" value="'.($options['autoplay'] ? 'true' : 'false').'" />'
              . '<param name="loop" value="'.($options['loop'] ? 'true' : 'false').'" />'
              . '<param name="scale" value="' . $options['scale'] . '" />'
              . '<embed src="'.$path.'" scale="' . $options['scale'] . '" width="'.$options['width'].'" height="'.$options['height'].'" controller="'.($options['controller'] ? 'true' : 'false').'" autoplay="'.($options['autoplay'] ? 'true' : 'false').'" pluginspage="http://www.apple.com/quicktime/download/" type="video/quicktime"></embed>'
              . '</object>';
        return $html;
    }
    
    /**
     * Default display of audio files via <object> tags.
     * 
     * @param File $file
     * @param array $options The set of default options for this includes:
     *  width, height, autoplay, controller, loop
     * @param string $type The Internet media type of the file
     * @return string
     */
    private function _audio($file, array $options, $type)
    {
        $path = html_escape($file->getWebPath('archive'));
        $html = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$options['width'].'" height="'.$options['height'].'">'
              . '<param name="src" value="'.$path.'" />'
              . '<param name="controller" value="'.($options['controller'] ? 'true' : 'false').'" />'
              . '<param name="autoplay" value="'.($options['autoplay'] ? 'true' : 'false').'" />'
              . '<param name="loop" value="'.($options['loop'] ? 'true' : 'false').'" />'
              . '<object type="' . $type . '" data="' . $path . '" width="'.$options['width'].'" height="'.$options['height'].'" autoplay="'.($options['autoplay'] ? 'true' : 'false').'">'
              . '<param name="src" value="'.$path.'" />'
              . '<param name="controller" value="'.($options['controller'] ? 'true' : 'false').'" />'
              . '<param name="autoplay" value="'.($options['autoplay'] ? 'true' : 'false').'" />'
              . '<param name="autostart" value="'.($options['autoplay'] ? '1' : '0').'" />'
              . '<param name="loop" value="'.($options['loop'] ? 'true' : 'false').'" />'
              . '</object>'
              . '</object>';
        return $html;
    }
    
    /**
     * Display OGG audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function ogg($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/ogg');
    }
    
    /**
     * Display MP3/MPEG audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function mp3($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/mpeg');
    }
    
    /**
     * Display AAC audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function aac($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/x-aac');
    }
    
    /**
     * Display AIFF audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function aiff($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/x-aiff');
    }
    
    /**
     * Display MIDI audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function midi($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/midi');
    }
    
    /**
     * Display MP4 audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function mp4($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/mp4');
    }
    
    /**
     * Display WAV audio files.
     * 
     * @param File $file
     * @param array $options
     * @return string
     */
    public function wav($file, array $options = array())
    {
        return $this->_audio($file, $options, 'audio/x-wav');
    }
    
    /**
     * Default display of an icon to represent a file.
     * 
     * Example usage:
     * 
     * echo display_files_for_item(array(
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
        $mimeType = $this->getMimeFromFile($file);
        $imgAttributes = (array)$options['imgAttributes'];
        // The path to the icon is keyed to the MIME type of the file.
        $imgAttributes['src'] = (string)$options['icons'][$mimeType];
        
        $html = '<img ' . _tag_attributes($imgAttributes) . ' />';
        
        if ($options['showFilename']) {
            // Add a div with arbitrary attributes.
            $html .= '<div ' . _tag_attributes((array)$options['filenameAttributes']) 
                   . '>' . html_escape($file->original_filename) . '</div>';
        }
        
        return $this->_linkToFile($html, $file, $options);
    }
    
    // END DEFINED DISPLAY CALLBACKS
    
    protected function getMimeFromFile($file)
    {
        return $file->getMimeType();
    }
    
    protected function getCallback($mimeType, $options, $fileExtension)
    {
        // Displaying icons overrides the default lookup mechanism.
        if (array_key_exists('icons', $options) and
                array_key_exists($mimeType, $options['icons'])) {
            return 'icon';
        }
        
        if (array_key_exists($mimeType, self::$_callbacks)) {
            $name = self::$_callbacks[$mimeType];
        } else if (array_key_exists($fileExtension, self::$_fileExtensionCallbacks)) {
            $name = self::$_fileExtensionCallbacks[$fileExtension];
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
        $callbackKey = !is_string($callback) ? serialize($callback) : $callback;
        if (array_key_exists($callbackKey, self::$_callbackOptions)) {
            return (array) self::$_callbackOptions[$callbackKey];
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
    public function media($file, array $props=array(), $wrapperAttributes = array())
    {
        $mimeType = $this->getMimeFromFile($file);
        $fileExtension = $this->_getFileExtension($file);
        
        // There is a chance that $props passed in could modify the callback
        // that is used.  Currently used to determine whether or not to display
        // an icon.
        $callback = $this->getCallback($mimeType, $props, $fileExtension);   
        
        $options = array_merge($this->getDefaultOptions($callback), $props);
        
        $html  = $this->getHtml($file, $callback, $options);
        
        // Append a class name that corresponds to the MIME type.
        if ($wrapperAttributes) {
            $mimeTypeClassName = str_ireplace('/', '-', $mimeType);
            if (array_key_exists('class', $wrapperAttributes)) {
                $wrapperAttributes['class'] .= ' ' . $mimeTypeClassName;
            } else {
                $wrapperAttributes['class']  = $mimeTypeClassName;
            }
        }
        
        //Wrap the HTML in a div with a class (if class is not set to null)
        $wrapper = !empty($wrapperAttributes) ? '<div ' . _tag_attributes($wrapperAttributes) . '>' : ''; 
        $html = !empty($wrapper) ? $wrapper . $html . "</div>" : $html;
        
        return apply_filters('display_file', $html, $file, $callback, $options, $wrapperAttributes);
    }
    
    private function _getFileExtension($file)
    {
        return pathinfo($file->original_filename, PATHINFO_EXTENSION);
    }
    
    /**
     * Return a valid img tag for a thumbnail image.
     */
    public function thumbnail($record, $props=array(), $width=null, $height=null) 
    {
        return $this->archive_image($record, $props, $width, $height, 'thumbnail');
    }
    
    /**
     * Return a valid img tag for a fullsize image.
     */
    public function fullsize($record, $props=array(), $width=null, $height=null)
    {
        return $this->archive_image($record, $props, $width, $height, 'fullsize');
    }
    
    /**
     * Return a valid img tag for a square_thumbnail image.
     */
    public function square_thumbnail($record, $props=array(), $width=null, $height=null)
    {
        return $this->archive_image($record, $props, $width, $height, 'square_thumbnail');
    }
    /**
     * Return a valid img tag for an image.
     */
    public function archive_image($record, $props, $width, $height, $format) 
    {
        if (!$record) {
            return false;
        }
        
        if ($record instanceof File) {
            $filename = $record->getDerivativeFilename();
            $file = $record;
        } else if ($record instanceof Item) {
            $item = $record;
            $file = get_db()->getTable('File')->getRandomFileWithImage($item->id);
            if (!$file) {
                return false;
            }
            $filename = $file->getDerivativeFilename();
        } else {
            // throw some exception?
            return '';
        }
        
        $uri = html_escape(file_display_uri($file, $format));
        
        /** 
         * Determine alt attribute for images
         * Should use the following in this order:
         * 1. alt option 
         * 2. file description
         * 3. file title 
         * 4. item title
         */
        $alt = '';
        if (isset($props['alt'])) {
            $alt = $props['alt'];
        } elseif ($fileDescription = item_file('Dublin Core', 'Description', array(), $file)) {
            $alt = $fileDescription;
        } elseif ($fileTitle = item_file('Dublin Core', 'Title', array(), $file)) {
            $alt = $fileTitle;
        } else if (isset($item)) {
            $alt = item('Dublin Core', 'Title', array(), $item);           
        }
        $props['alt'] = $alt;
        
        // Build the img tag
        $html = '<img src="' . $uri . '" '._tag_attributes($props) . '/>' . "\n";
        
        return $html;
    }
}
