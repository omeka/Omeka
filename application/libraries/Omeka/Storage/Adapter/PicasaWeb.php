<?php
/**
 * @copyright Klokan Technologies GmbH, 2012
 * @author Petr Pridal
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Simple cloud storage adapter for PicasaWeb. Uses the 'original_filename'
 * to store the PicasaWeb URL.
 *
 * TODO: Implement upload to the PicasaWeb storage with the help of
 * http://framework.zend.com/manual/en/zend.gdata.photos.html
 *
 * @package Omeka
 */
class Omeka_Storage_Adapter_PicasaWeb implements Omeka_Storage_Adapter
{

    //const GOOGLE_ACCOUNT = 'googleAccount';
    //const GOOGLE_PASSWORD = 'googlePassword';

    /**
     * @var Zend_Gdata_Gbase 
     */
    //private $_service;

    /**
     * @var array
     */
    private $_options;

    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_options = $options;

	/*
        if (array_key_exists(self::GOOGLE_ACCOUNT, $options)
        && array_key_exists(self::GOOGLE_PASSWORD, $options)) {
            $googleAccount = $options[self::GOOGLE_ACCOUNT];
            $googlePassword = $options[self::GOOGLE_PASSWORD];
        } else {
            throw new Omeka_Storage_Exception('You must specify your Google Account and Password to use the PicasaWeb storage adapter.');
        }
	// Parameters for ClientAuth authentication
        $auth = Zend_Gdata_Gbase::AUTH_SERVICE_NAME;

        // Create an authenticated HTTP client
        $client = Zend_Gdata_ClientLogin::getHttpClient($googleAccount, $googlePassword, $auth);

        // Create an instance of the Base service 
        $this->_service = new Zend_Gdata_Photos($client);
	*/
    }

    public function setUp()
    {
        // Required by interface but does nothing, for the time being.
    }

    public function canStore()
    {
        return false;
    }

    /**
     * @param string $source Local filesystem path to file.
     * @param string $dest Destination path.
     */
    public function store($source, $dest)
    {
        // Required by interface but does nothing, for the time being.

        /*
        // If it does not exists, then create first album with name:
	// "Omeka-" + file ID_AUTOINCREMENT int_divide 1000
        $albumEntry = new Zend_Gdata_Photos_AlbumEntry();
        $albumEntry->setTitle($service->newTitle("Omeka-$i"));
        $service->insertAlbumEntry($albumEntry);

        $fd = $service->newMediaFileSource($source);
        $fd->setContentType('image/jpeg');
 
        $entry = new Zend_Gdata_Photos_PhotoEntry();
        $entry->setMediaSource($fd);
        // $entry->setTitle($service->newTitle($photo["name"]));

        $service->insertPhotoEntry($entry, $albumEntry);
	*/
    }

    /**
     * @param string $source Original stored path.
     * @param string $dest Destination stored path.
     */
    public function move($source, $dest)
    {
        // Required by interface but does nothing, for the time being.
    }

    /**
     * @param string $path
     */
    public function delete($path)
    {
        // Required by interface but does nothing, for the time being.
    }

    /**
     * Get a URI for a "stored" file.
     *
     * @param string $path
     * @return string URI
     */
    public function getUri($path)
    {
	list( $size, $filename ) = explode('/', $path, 2);

	$mapping = array(
'square_thumbnails' => 's150-c', // 200
'thumbnails' => 's150', // 200
'fullsize' => 's950', // 980
'files' => 'd',
'archive' => 's1600' );

	// !!! LOAD THE PICASA WEB URL FROM ORIGINAL_FILENAME FIELD !!!

	$db = get_db();
	$res = $db->fetchRow("SELECT original_filename FROM {$db->prefix}files WHERE archive_filename = '{$filename}'");
	return $res['original_filename'].$mapping[$size]."/$filename";
    }

}
