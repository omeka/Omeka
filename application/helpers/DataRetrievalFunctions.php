<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DataRetrievalHelpers
 */

/**
 * Returns the total number of results
 *
 * @return integer
 */
function total_results()
{
    if(Zend_Registry::isRegistered('total_results')) {
        $count = Zend_Registry::get('total_results');

        return $count;
    }
}

/**
 * Retrieve the latest available version of Omeka by accessing the appropriate
 * URI on omeka.org.
 *
 * @since 1.0
 * @return string|false The latest available version of Omeka, or false if the
 * request failed for some reason.
 */
function get_latest_omeka_version()
{
    $omekaApiUri = 'http://api.omeka.org/latest-version';
    $omekaApiVersion = '0.1';

    // Determine if we have already checked for the version lately.
    $check = unserialize(get_option('omeka_update')) or $check = array();
    // This a timestamp corresponding to the last time we checked for
    // a new version.  86400 is the number of seconds in a day, so check
    // once a day for a new version.
    if (array_key_exists('last_updated', $check)
        and ($check['last_updated'] + 86400) > time()) {
        // Return the value we got the last time we checked.
        return $check['latest_version'];
    }

    try {
        $client = new Zend_Http_Client($omekaApiUri);
        $client->setParameterGet('version', $omekaApiVersion);
        $client->setMethod('GET');
        $result = $client->request();
        if ($result->getStatus() == '200') {
            $latestVersion = $result->getBody();
            // Store the newer values
            $check['latest_version'] = $latestVersion;
            $check['last_updated'] = time();
            set_option('omeka_update', serialize($check));
           return $result->getBody();
        } else {
           debug("Attempt to GET $omekaApiUri with version=$omekaApiVersion "
                 . "returned with status=" . $result->getStatus() . " and "
                 . "response body=" . $result->getBody());
        }
    } catch (Exception $e) {
        debug('Error in retrieving latest Omeka version: ' . $e->getMessage());
    }
    return false;
}
