<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_MediaAdapter
{
    /**
     * @see Omeka_Plugin_Broker::addMediaAdapter()
     *
     * @var array
     **/
    protected $_media = array('callbacks'=>array(), 'options'=>array());
    
    /**
     * Adds a plugin hook to display files of a specific MIME type in a certain way.
     * 
     * This allows plugins to hook directly into the Omeka_View_Helper_Media
     * class, so that plugins can override/define ways of displaying specific
     * files.  The most obvious example of where this would come in handy is
     * to define ways of displaying uncommon files, such as QTVR, or novel ways
     * of displaying more common files, such as using iPaper to display PDFs.
     *
     * The advantage is seemless integration with the themes, rather than
     * forcing theme designers to use plugin-specific API calls in their themes.
     *
     * @internal This operates on two keyed lists: a list of callbacks, which is
     * keyed to the MIME type, i.e. array('video/wmv'=>'foobar_movie_display').
     * The second list is the set of default options for the callback, which
     * can be overridden during the actual display_files() call within the theme.
     * 
     * @param array|string $mimeTypes Set of MIME types that this specific
     * callback will respond to.
     * @param callback Any valid callback.  This function should return a
     * string containing valid XHTML, which will be used to display the file.
     * @return void
     **/
    public function addMediaAdapter($mimeTypes, $callback, array $defaultOptions = array())
    {
        //Create the keyed list of mimeType=>callback format, and merge it
        //with the current list.
        $mimeTypes = (array) $mimeTypes;
        $fillArray = array_fill(0, count($mimeTypes), $callback);    
        $callbackList = array_combine($mimeTypes, $fillArray);
        
        $this->_media['callbacks'] = array_merge($callbackList, $this->_media['callbacks']);
        
        //Create the keyed list of callback=>options format, and add it 
        //to the current list
        
        //The key for the array might be the serialized callback (if necessary)
        $callbackKey = !is_string($callback) ? serialize($callback) : $callback;
        $this->_media['options'][$callbackKey] = $defaultOptions;        
    }
    
    /**
     * Retrieve a list of all media display callbacks that are defined by
     * plugins.  Currently called only within Omeka_View_Helper_Media
     *
     * @see Omeka_View_Helper_Media::__construct()
     * @return array
     **/
    public function getMediaAdapters()
    {        
        return $this->_media;
    }
}
