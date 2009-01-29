<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
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
 * @package OmekaThemes
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
        'image/tiff'=>'image', 
        'image/jpeg'=>'image',
        'image/png'=>'image',
        'image/tiff'=>'image',
	    'video/avi'=>'wmv',
	    'video/msvideo'=>'wmv',
	    'video/x-msvideo'=>'wmv',
	    'video/x-ms-wmv'=>'wmv',
	    'video/quicktime'=>'mov',
		'video/mp4'=>'mov',
	    'video/mpeg'=>'mov',
	    'audio/x-wav'=>'audio',
	    'audio/mpeg'=>'audio',
	    'application/ogg'=>'audio',
	    'audio/x-midi'=>'audio'
	    );
	    
    /**
     * The array consists of the default options
     * which are passed to the callback.
     *
     * @var array
     **/
    protected $_callbackOptions = array(
        'image'=>array(
            'imageSize'=>'square_thumbnail',
            'linkToFile'=>true
            ),
        'wmv'=>array(
			'width' => '320', 
			'height' => '240', 
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
		    'linkAttributes' => array(),
		    'imgAttributes' => array(),
		    'filenameAttributes' => array()
		    ));
        
    protected $_wrapperClass = null;
    
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
        $linkAttributes = array('href'=>file_download_uri($file), 
                            'class'=>'download-file');
        
        $linkAttributes = array_merge($linkAttributes, (array)$options['linkAttributes']);
        $html .= '<a ' . _tag_attributes($linkAttributes) . '>'. $file->original_filename. '</a>';   
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
        $item_title = item('Dublin Core', 'Title');
        if ($options['linkToFile']) {
            $defaultLinkAttributes = array(
                'class'=>'download-file', 
                'href'=>file_download_uri($file));
            $linkAttributes = array_merge($defaultLinkAttributes, (array)$options['linkAttributes']);
            
            $html .= '<a ' . _tag_attributes($linkAttributes) . '>';
        }
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
        elseif (!empty($item_title)) {
            $alt = $item_title;
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
            $imgHtml = $imageSize($file, $imgAttributes);
        }    
		
		$html .= !empty($imgHtml) ? $imgHtml : htmlentities($file->original_filename);	
		
		if ($options['linkToFile']) {
		  $html .= '</a>';
		}
		
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
		$path = $file->getWebPath('archive');
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
		$path = $file->getWebPath('archive');

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
          <param name="src" value="' . $path . '">
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
                   . '>' . htmlspecialchars($file->original_filename) . '</div>';
        }
        
        // Wrap with an <a href> if necessary.
        if ($options['linkToFile']) {
            $linkAttributes = array('href'=>file_download_uri($file));
            $linkAttributes = array_merge($linkAttributes, (array)$options['linkAttributes']);
            $html = '<a ' . _tag_attributes($linkAttributes) . '>' . $html
                  . '</a>';
        }
        return $html;
    }
    
    // END DEFINED DISPLAY CALLBACKS
    
    public function setWrapperClass($class)
    {
        $this->_wrapperClass = $class;
    }
    
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
    public function media($file, array $props=array())
    {		
        $mimeType = $this->getMimeFromFile($file);
        // There is a chance that $props passed in could modify the callback
        // that is used.  Currently used to determine whether or not to display
        // an icon.
        $callback = $this->getCallback($mimeType, $props);	 
        
        $options = array_merge($this->getDefaultOptions($callback), $props);
        
        $html  = $this->getHtml($file, $callback, $options);

        //Wrap the HTML in a div with a class (if class is not set to null)
        $wrapper = $this->_wrapperClass ? '<div class="' . $this->_wrapperClass . '">' : ''; 
        
		$html = !empty($wrapper) ? $wrapper . $html . "</div>" : $html;
		
		return $html;
    }
}
