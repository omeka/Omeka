<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 **/
 
/**
 * View Helper for displaying files through Omeka.  
 * 
 * This will determine how to display any given file based on the MIME type 
 * of that file.  Individual rendering agents are defined by callbacks that
 * are either contained within this class or defined by plugins.  Callbacks
 * defined by plugins will override native class methods if defined for 
 * existing MIME types.  In order to define a rendering callback that should
 * be in the core of Omeka, define a method in this class and then make sure
 * that it responds to all the correct MIME types by modifying other properties
 * in this class.
 * 
 *
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View_Helper_Media
{   
    /**
     * Array of MIME types and the callbacks that can process it.
     *
     * Example:
     *
     * array('video/avi'=>'wmv');
     *
     * @var array
     **/
    protected $_callbacks = array(
	    'application/ogg'   => 'audio',
	    'audio/aac'         => 'audio',
	    'audio/aiff'        => 'audio',
	    'audio/midi'        => 'audio',
	    'audio/mp3'         => 'audio',
        'audio/mp4'         => 'audio',
        'audio/mpeg'        => 'audio',
        'audio/mpeg3'       => 'audio',
        'audio/mpegaudio'   => 'audio',
        'audio/mpg'         => 'audio',
	    'audio/ogg'         => 'audio',
	    'audio/wav'         => 'audio',
	    'audio/x-mp3'       => 'audio',
        'audio/x-mp4'       => 'audio',
        'audio/x-mpeg'      => 'audio',
        'audio/x-mpeg3'     => 'audio',
	    'audio/x-midi'      => 'audio',
	    'audio/x-mpegaudio' => 'audio',
	    'audio/x-mpg'       => 'audio',
	    'audio/x-ogg'       => 'audio',
	    'audio/x-wav'       => 'audio',
	    'audio/x-aac'       => 'audio',
	    'audio/x-aiff'      => 'audio',
	    'audio/x-midi'      => 'audio',
	    'audio/x-mp3'       => 'audio',
        'audio/x-mp4'       => 'audio',
        'audio/x-mpeg'      => 'audio',
        'audio/x-mpeg3'     => 'audio',
        'audio/x-mpegaudio' => 'audio',
        'audio/x-mpg'       => 'audio',	    
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
	    'video/x-ms-wmv'    => 'wmv'	    
	    );
	    
    /**
     * The array consists of the default options
     * which are passed to the callback.
     *
     * @var array
     **/
    protected $_callbackOptions = array(
        'defaultDisplay'=>array(
            'linkToFile'=>true,
            'linkToMetadata'=>false
            ),
        'image'=>array(
            'imageSize'=>'square_thumbnail',
            'linkToFile'=>true,
            'linkToMetadata'=>false
            ),
        'wmv'=>array(
			'width' => '320', 
			'height' => '240', 
			'autostart' => 0, 
			'ShowControls'=> 1, 
			'ShowDisplay'=> 1,
			'ShowStatusBar' => 1
			),
		'wma'=>array(
			'width' => '320', 
			'height' => '46', 
			'autostart' => 0, 
			'ShowControls'=> 1, 
			'ShowDisplay'=> 1,
			'ShowStatusBar' => 1
			),
		'mov'=>array(
			'width' => '320', 
			'height' => '240', 
			'autoplay' => 0, 
			'controller'=> 1, 
			'loop'=> 0
			),
		'audio'=>array(
		    'autoplay' => 'false',
		    'autoStart' => 0,
		    'width' => 200,
		    'height' => 20
		    ),
		'icon'=>array(
		    'showFilename' => true,
		    'icons' => array(),
		    'linkToFile' => true,
		    'linkToMetadata' => false,
		    'linkAttributes' => array(),
		    'imgAttributes' => array(),
		    'filenameAttributes' => array()
		    ));
            
    /**
     * Retrieve all the callbacks and default options from the plugins.  They
     * should be merged with the defaults such that plugins take precedence.
     * 
     * @uses Omeka_Context
     * @uses Omeka_Plugin_Broker::getMediaAdapters()
     * @return void
     **/
    public function __construct()
    {
       $pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
       
       if($pluginBroker) {
           $info = $pluginBroker->getMediaAdapters();
           
           //Merge all the plugin callbacks with the ones in this object
           
           $this->_callbacks = array_merge($this->_callbacks, $info['callbacks']);
           $this->_callbackOptions = array_merge($this->_callbackOptions, $info['options']);           
       }
    }
    
    /**
     * Default display for MIME types that do not have a valid rendering
     * callback.  
     *
     * This wraps the original filename in a link to download that file,
     * with a class of "download-file".  Any behavior more complex than
     * that should be processed with a valid callback.
     * 
     * @param File
     * @param array
     * @return string HTML
     **/
    public function defaultDisplay($file, array $options=array())
    {
        return $this->_linkToFile(null, $file, $options);   
    }
    
    /**
     * Add a link for the file based on the given set of options.
     * 
     * If the 'linkToMetadata' option is true, then link to the file metadata
     * page (files/show).  Otherwise if 'linkToFile' is true, link to download
     * the file.  Otherwise just return the $html without wrapping in a link.
     * 
     * The attributes for the link will be based off the 'linkAttributes' option,
     * which should be an array.
     * 
     * If $html is null, it defaults to original filename of the file.
     * 
     * @param string
     * @param File
     * @param array
     * @return string
     **/
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
            $linkAttributes = array_merge($defaultLinkAttributes, (array)$options['linkAttributes']);

            $html = '<a ' . _tag_attributes($linkAttributes) . '>' . $html . '</a>';
		}
		
		return $html;
    }
    
    /**
     * Returns valid XHTML markup for displaying an image that has been
     * archived through Omeka.  
     * 
     * @param File
     * @param array Options for customizing the display of images.  Current
     * options include: 'imageSize'
     * @return string HTML for display
     **/

    
    public function image($file, array $options=array())
    {
        $html = '';
        
        /** 
         * Setting a variable for content for the alt attribute for images.
         * Problem with alternative text is that it shoudl describe what's going
         * on in the image, so should use the file description first. Item title 
         * usually doesn't describe what's in the image specifically, but is provided 
         * as a last resort.
         **/
        if (!empty($file->description)) {
            $alt = $file->description;
        }
        elseif (!empty($file->title)) {
            $alt = $file->title;
        }
        else {
            try {
                $alt = $item_title = item('Dublin Core', 'Title');
                //  Suppress errors b/c get_current_item()
                // throws an exception.  There should be a has_current_item() helper
                // to avoid this sort of thing.    
            } catch (Exception $e) {} 
        }
        
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
            // A class is given to all of the images by default to make it easier to style.
            // This can be modified by passing it in as an option, but recommended
            // against.  Can also modify alt text via an option.
            $imgClass = $imgClasses[$imageSize];
            
            $imgAttributes = array_merge(array('class'=>$imgClass, 'alt'=>$alt),
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
     * @param File
     * @param array Options
     * @return string
     **/ 
    public function wmv($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));
		$html 	.= 	'<object id="MediaPlayer" width="'.$options['width'].'" height="'.$options['height'].'"';
		$html 	.= 	' classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95"';
		$html 	.=	' standby="Loading Windows Media Player components..." type="application/x-oleobject">'."\n";
		$html	.=	'<param name="FileName" value="'.$path.'" />'."\n";
		$html	.=	'<param name="AutoPlay" value="'.($options['autostart'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowControls" value="'.($options['ShowControls'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowStatusBar" value="'.($options['ShowStatusBar'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowDisplay" value="'.($options['ShowDisplay'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<embed type="application/x-mplayer2" src="'.$path.'" name="MediaPlayer"';
		$html	.=	' width="'.$options['width'].'" height="'.$options['height'].'"'; 		
		$html	.=	' ShowControls="'.$options['ShowControls'].'" ShowStatusBar="'.$options['ShowStatusBar'].'"'; 
		$html	.=	' ShowDisplay="'.$options['ShowDisplay'].'" autoplay="'.$options['autostart'].'"></embed></object>';     
		
		return $html;   
    } 
    
    /**
     * Retrieve valid XHTML for displaying a wma audio file or equivalent.  
     * Currently this loads the video inside of an <object> tag, but that
     * provides less flexibility than a flash wrapper, which seems to be a 
     * standard Web2.0 practice for video sharing.  This limitation can be
     * overcome by a plugin that used a flash wrapper for displaying video.
     * 
     * @param File
     * @param array Options
     * @return string
     **/ 
    public function wma($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));
		$html 	.= 	'<object id="MediaPlayer" width="'.$options['width'].'" height="'.$options['height'].'"';
		$html 	.= 	' classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95"';
		$html 	.=	' standby="Loading Windows Media Player components..." type="application/x-oleobject">'."\n";
		$html	.=	'<param name="FileName" value="'.$path.'" />'."\n";
		$html	.=	'<param name="AutoPlay" value="'.($options['autostart'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowControls" value="'.($options['ShowControls'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowStatusBar" value="'.($options['ShowStatusBar'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<param name="ShowDisplay" value="'.($options['ShowDisplay'] ? 'true' : 'false').'" />'."\n";
		$html	.=	'<embed type="application/x-mplayer2" src="'.$path.'" name="MediaPlayer"';
		$html	.=	' width="'.$options['width'].'" height="'.$options['height'].'"'; 		
		$html	.=	' ShowControls="'.$options['ShowControls'].'" ShowStatusBar="'.$options['ShowStatusBar'].'"'; 
		$html	.=	' ShowDisplay="'.$options['ShowDisplay'].'" autoplay="'.$options['autostart'].'"></embed></object>';     
		
		return $html;   
    }
    
    /**
     * Retrieve valid XHTML for displaying Quicktime video files
     * 
     * @param string
     * @return void
     **/ 
    public function mov($file, array $options=array())
    {
        $path = html_escape($file->getWebPath('archive'));

		$html = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$options['width'].'" height="'.$options['height'].'">
			<param name="src" value="'.$path.'" />
			<param name="controller" value="'.($options['controller'] ? 'true' : 'false').'" />
			<param name="autoplay" value="'.($options['autoplay'] ? 'true' : 'false').'" />
			<param name="loop" value="'.($options['loop'] ? 'true' : 'false').'" />

			<embed src="'.$path.'" scale="tofit" width="'.$options['width'].'" height="'.$options['height'].'" controller="'.($options['controller'] ? 'true' : 'false').'" autoplay="'.($options['autoplay'] ? 'true' : 'false').'" pluginspage="http://www.apple.com/quicktime/download/" type="video/quicktime"></embed>
			</object>';
			
		return $html;        
    } 
    
    /**
     * Default display of audio files via <object> tags.
     * 
     * @param File
     * @param array $options The set of default options for this includes:
     * 'autoplay', 'autoStart', 'width', 'height'
     * @return string
     **/
    public function audio($file, array $options=array())
    {
        $path = $file->getWebPath('archive');
        
        $linkAttributes = array_merge(array('href'=>$path), (array)$options['linkAttributes']);
        $html = '<object type="'. $file->mime_browser . '" data="' . $path . 
        '" width="' . $options['width'] . '" height="' . $options['height'] . '">
          <param name="src" value="' . html_escape($path) . '">
          <param name="autoplay" value="' . $options['autoplay'] . '">
          <param name="autoStart" value="' . $options['autoStart'] . '">
          alt : <a ' . _tag_attributes($linkAttributes) . '>' . $file->original_filename . '</a>
        </object>';
        
        return $html;
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
     **/
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
        return $file->mime_browser;
    }
    
    protected function getCallback($mimeType, $options)
    {
        // Displaying icons overrides the default lookup mechanism.
        if (array_key_exists('icons', $options) and
                array_key_exists($mimeType, $options['icons'])) {
            return 'icon';
        }
        
        $name = $this->_callbacks[$mimeType];
        if(!$name) {
            $name = 'defaultDisplay';
        }
        return $name;
    }
    
    /**
     * @see Omeka_Plugin_Broker::addMediaAdapter()
     * @param mixed
     * @return array
     **/
    protected function getDefaultOptions($callback)
    {
        $callbackKey = !is_string($callback) ? serialize($callback) : $callback;
        return (array) $this->_callbackOptions[$callbackKey];
    }
    
    /**
     * Retrieve the HTML for a given file from the callback.   
     * 
     * @param File 
     * @param callback Any valid callback that will display the HTML.
     * @param array Set of options passed to the rendering callback.
     * @return string HTML for displaying the file.
     **/
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
     * 
     * @param File
     * @param array Set of options passed by a theme writer to the customize
     * the display of any given callback.
     * @return string HTML
     **/
    public function media($file, array $props=array(), $wrapperAttributes = array())
    {		
        $mimeType = $this->getMimeFromFile($file);
        // There is a chance that $props passed in could modify the callback
        // that is used.  Currently used to determine whether or not to display
        // an icon.
        $callback = $this->getCallback($mimeType, $props);	 
        
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
		
		return $html;
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
            $file = get_db()->getTable('File')->getRandomFileWithImage($record->id);
            if (!$file) {
                return false;
            }
            $filename = $file->getDerivativeFilename();
        }

        $path = $file->getPath($format);
        $uri = html_escape(file_display_uri($file, $format));
        
        if (!file_exists($path)) {
            return false;
        }

        list($oWidth, $oHeight) = getimagesize( $path );
        if (!$width && !$height) {
            $width = $oWidth;
            $height = $oHeight;
        } else if ($oWidth > $width && !$height) {
            $ratio = $width / $oWidth;
            $height = $oHeight * $ratio;
        } else if (!$width && $oHeight > $height) {
            $ratio = $height / $oHeight;
            $width = $oWidth * $ratio;
        }
        $props['width'] = $width;
        $props['height'] = $height;
        
        $html = '<img src="' . $uri . '" '._tag_attributes($props) . '/>' . "\n";
        return $html;
    }
}
