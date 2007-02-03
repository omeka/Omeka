<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Feed_Exception
 */
require_once 'Zend/Feed/Exception.php';

/**
 * Zend_Feed_Atom
 */
require_once 'Zend/Feed/Atom.php';

/**
 * Zend_Feed_Rss
 */
require_once 'Zend/Feed/Rss.php';

/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';


/**
 * Feed utility class
 *
 * Base Zend_Feed class, containing constants and the Zend_Http_Client instance
 * accessor.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed
{

    /**
     * HTTP client object to use for retrieving feeds
     *
     * @var Zend_Http_Client
     */
    protected static $_httpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    /**
     * @var array
     */
    protected static $_namespaces = array(
        'opensearch' => 'http://a9.com/-/spec/opensearchrss/1.0/',
        'atom' => 'http://www.w3.org/2005/Atom',
        'rss' => 'http://blogs.law.harvard.edu/tech/rss',
    );


    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.  If none
     * is set, the default Zend_Http_Client will be used.
     *
     * @param Zend_Http_Client_Abstract $httpClient
     */
    public static function setHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object.
     *
     * @return Zend_Http_Client
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof Zend_Http_Client) {
            self::$_httpClient = new Zend_Http_Client();
        }

        return self::$_httpClient;
    }


    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param boolean $override Whether to override PUT and DELETE.
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }


    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }


    /**
     * Get the full version of a namespace prefix
     *
     * Looks up a prefix (atom:, etc.) in the list of registered
     * namespaces and returns the full namespace URI if
     * available. Returns the prefix, unmodified, if it's not
     * registered.
     *
     * @return string
     */
    public static function lookupNamespace($prefix)
    {
        return isset(self::$_namespaces[$prefix]) ?
            self::$_namespaces[$prefix] :
            $prefix;
    }


    /**
     * Add a namespace and prefix to the registered list
     *
     * Takes a prefix and a full namespace URI and adds them to the
     * list of registered namespaces for use by
     * Zend_Feed::lookupNamespace().
     *
     * @param string $prefix The namespace prefix
     * @param string $namespaceURI The full namespace URI
     */
    public static function registerNamespace($prefix, $namespaceURI)
    {
        self::$_namespaces[$prefix] = $namespaceURI;
    }


    /**
     * Imports a feed located at $uri.
     *
     * @param string $uri
     * @throws Zend_Feed_Exception
     * @return Zend_Feed_Abstract
     */
    public static function import($uri)
    {
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->request('GET');
        if ($response->getStatus() !== 200) {
            throw new Zend_Feed_Exception('Feed failed to load, got response code ' . $response->getStatus());
        }
        $feed = $response->getBody();
        return self::importString($feed);
    }


    /**
     * Imports a feed represented by $string.
     *
     * @param string $string
     * @throws Zend_Feed_Exception
     * @return Zend_Feed_Abstract
     */
    public static function importString($string)
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$success) {
            throw new Zend_Feed_Exception("DOMDocument cannot parse XML: $php_errormsg");
        }

        // Try to find the base feed element or a single <entry> of an Atom feed
        if ($doc->getElementsByTagName('feed')->item(0) ||
            $doc->getElementsByTagName('entry')->item(0)) {
            // return a newly created Zend_Feed_Atom object
            return new Zend_Feed_Atom(null, $string);
        }

        // Try to find the base feed element of an RSS feed
        if ($doc->getElementsByTagName('channel')->item(0)) {
            // return a newly created Zend_Feed_Rss object
            return new Zend_Feed_Rss(null, $string);
        }

        // $string does not appear to be a valid feed of the supported types
        throw new Zend_Feed_Exception('Invalid or unsupported feed format');
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param string $uri
     * @throws Zend_Feed_Exception
     * @return Zend_Feed_Abstract
     */
    public static function importFile($filename)
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename);
        @ini_restore('track_errors');
        if ($feed === false) {
            throw new Zend_Feed_Exception("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed);
    }


    /**
     * Attempts to find feeds at $uri referenced by <link ... /> tags. Returns an
     * array of the feeds referenced at $uri.
     *
     * @todo Allow findFeeds() to follow one, but only one, code 302.
     *
     * @param string $uri
     * @throws Zend_Feed_Exception
     * @return array
     */
    public static function findFeeds($uri)
    {
        // Get the HTTP response from $uri and save the contents
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->request();
        if ($response->getStatus() !== 200) {
            throw new Zend_Feed_Exception("Failed to access $uri, got response code " . $response->getStatus());
        }
        $contents = $response->getBody();

        // Parse the contents for appropriate <link ... /> tags
        @ini_set('track_errors', 1);
        $pattern = '~(<link[^>]+)/?>~i';
        $result = @preg_match_all($pattern, $contents, $matches);
        @ini_restore('track_errors');
        if ($result === false) {
            throw new Zend_Feed_Exception("Internal error: $php_errormsg");
        }

        // Try to fetch a feed for each link tag that appears to refer to a feed
        $feeds = array();
        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $link) {
                $xml = @simplexml_load_string(rtrim($link, ' /') . ' />');
                if ($xml === false) {
                    continue;
                }
                $attributes = $xml->attributes();
                if (!isset($attributes['rel']) || !@preg_match('~^(?:alternate|service\.feed)~i', $attributes['rel'])) {
                    continue;
                }
                if (!isset($attributes['type']) ||
                        !@preg_match('~^application/(?:atom|rss|rdf)\+xml~', $attributes['type'])) {
                    continue;
                }
                if (!isset($attributes['href'])) {
                    continue;
                }
                try {
                    $feed = self::import($attributes['href']);
                } catch (Exception $e) {
                    continue;
                }
                $feeds[] = $feed;
            }
        }

        // Return the fetched feeds
        return $feeds;
    }

}
