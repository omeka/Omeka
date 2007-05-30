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
 * @package    Zend_Db
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/**
 * Zend_Db_Profiler_Query
 */
require_once 'Zend/Db/Profiler/Query.php';

/**
 * Zend_Db_Profiler_Exception
 */
require_once 'Zend/Db/Profiler/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Profiler
{

    /**
     * A connection operation or selecting a database.
     */
    const CONNECT = 1;

    /**
     * Any general database query that does not fit into the other constants.
     */
    const QUERY = 2;

    /**
     * Adding new data to the database, such as SQL's INSERT.
     */
    const INSERT = 4;

    /**
     * Updating existing information in the database, such as SQL's UPDATE.
     *
     */
    const UPDATE = 8;

    /**
     * An operation related to deleting data in the database,
     * such as SQL's DELETE.
     */
    const DELETE = 16;

    /**
     * Retrieving information from the database, such as SQL's SELECT.
     */
    const SELECT = 32;

    /**
     * Transactional operation, such as start transaction, commit, or rollback.
     */
    const TRANSACTION = 64;


    /**
     * Array of Zend_Db_Profiler_Query objects.
     *
     * @var Zend_Db_Profiler_Query
     */
    protected $_queryProfiles = array();

    /**
     * Stores enabled state of the profiler.  If set to False, calls to
     * queryStart() will simply be ignored.
     *
     * @var bool
     */
    protected $_enabled = false;

    /**
     * Stores the number of seconds to filter.  NULL if filtering by time is
     * disabled.  If an integer is stored here, profiles whose elapsed time
     * is less than this value in seconds will be unset from
     * the self::$_queryProfiles array.
     *
     * @var integer
     */
    protected $_filterElapsedSecs = null;

    /**
     * Logical OR of any of the filter constants.  NULL if filtering by query
     * type is disable.  If an integer is stored here, it is the logical OR of
     * any of the query type constants.  When the query ends, if it is not
     * one of the types specified, it will be unset from the
     * self::$_queryProfiles array.
     *
     * @var integer
     */
    protected $_filterTypes = null;


    /**
     * Class constructor.  The profiler is disabled by default unless it is
     * specifically enabled by passing in $enabled here or calling setEnabled().
     *
     * @param bool $enabled
     */
    public function __construct($enabled=false)
    {
        $this->setEnabled($enabled);
    }


    /**
     * Enable or disable the profiler.  If $enable is false, the profiler
     * is disabled and will not log any queries sent to it.
     *
     * @param bool $enable
     * @return bool
     */
    public function setEnabled($enable)
    {
        $this->_enabled = $enable;
        return true;
    }


    /**
     * Sets a minimum number of seconds for saving query profiles.  If this
     * is set, only those queries whose elapsed time is equal or greater than
     * $minimumSeconds will be saved.  To save all queries regardless of
     * elapsed time, set $minimumSeconds to null.
     *
     * @param int $minimumSeconds
     * @return bool
     */
    public function setFilterElapsedSecs($minimumSeconds=null)
    {
        if (is_null($minimumSeconds)) {
            $this->_filterElapsedSecs = null;
        }

        $this->_filterElapsedSecs = $minimumSeconds;
        return true;
    }


    /**
     * Sets the types of query profiles to save.  Set $queryType to one of
     * the Zend_Db_Profiler::* constants to only save profiles for that type of
     * query.  To save more than one type, logical OR them together.  To
     * save all queries regardless of type, set $queryType to null.
     *
     * @param null|integer $queryTypes
     */
    public function setFilterQueryType($queryTypes=null)
    {
        $this->_filterTypes = $queryTypes;
        return true;
    }


    /**
     * Get the current state of enable.  If True is returned,
     * the profiler is enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }


    /**
     * Clears the history of any past query profiles.  This is unrelentless
     * and will even clear queries that were started and may not have
     * been marked as ended.
     *
     * @return unknown
     */
    public function clear()
    {
        $this->_queryProfiles = array();
        return true;
    }


    /**
     * Start a query.  Creates a new query profile object (Zend_Db_Profiler_Query)
     * and returns the "query profiler handle".  Run the query, then call
     * queryEnd() and pass it this handle to make the query as ended and
     * record the time.  If the profiler is not enabled, this takes no
     * action and immediately runs.
     *
     * @param string $queryText     SQL statement
     * @param int $queryType        Type of query, one of the Zend_Db_Profiler::* constants
     * @return mixed
     */
    public function queryStart($queryText, $queryType=null)
    {
        if (!$this->_enabled) {
            return null;
        }

        // make sure we have a query type
        if (is_null($queryType)) {
            $initial = substr($queryText, 0, 6);
            switch (strtolower($initial)) {
                case 'insert':
                    $queryType = self::INSERT;
                    break;
                case 'update':
                    $queryType = self::UPDATE;
                    break;
                case 'delete':
                    $queryType = self::DELETE;
                    break;
                case 'select':
                    $queryType = self::SELECT;
                    break;
                default:
                    $queryType = self::QUERY;
                    break;
            }
        }

        $this->_queryProfiles[] = new Zend_Db_Profiler_Query($queryText, $queryType);
        end($this->_queryProfiles);
        return key($this->_queryProfiles);
    }


    /**
     * Ends a query.  Pass it the handle that was returned by queryStart().
     * This will mark the query as ended and save the time.
     *
     * @param integer $queryId
     * @throws Zend_Db_Profiler_Exception
     * @return boolean
     */
    public function queryEnd($queryId)
    {
        /* @var $qp Zend_Db_Profiler_Query */

        // Don't do anything if the Zend_Db_Profiler is not enabled.
        if (!$this->_enabled) {
            return true;
        }

        // Check for a valid query handle.
        $qp = $this->_queryProfiles[$queryId];
        if ($qp->hasEnded()) {
            throw new Zend_Db_Profiler_Exception('Query with profiler handle "'
                                          . $queryId .'" has already ended.');
        }

        // End the query profile so that the elapsed time can be calculated.
        $qp->end();

        /**
         * If filtering by elapsed time is enabled, only keep the profile if
         * it ran for the minimum time.
         */
        if (!is_null($this->_filterElapsedSecs)) {
            if ($qp->getElapsedSecs() < $this->_filterElapsedSecs) {
                unset($this->_queryProfiles[$queryId]);
            }
        }

        /**
         * If filtering by query type is enabled, only keep the query if
         * it was one of the allowed types.
         */
        if (!is_null($this->_filterTypes)) {
            if (!($qp->getQueryType() & $this->_filterTypes)) {
                unset($this->_queryProfiles[$queryId]);
            }
        }

        return true;
    }


    /**
     * Get a profile for a query.  Pass it the same handle that was returned
     * by queryStart() and it will return a Zend_Db_Profiler_Query object.
     *
     * @param int $queryId
     * @throws Zend_Db_Profiler_Exception
     * @return Zend_Db_Profiler_Query
     */
    public function getQueryProfile($queryId)
    {
        if (!array_key_exists($queryId, $this->_queryProfiles)) {
            throw new Zend_Db_Profiler_Exception("Query handle \"$queryId\" not found in profiler log.");
        }

        return $this->_queryProfiles[$queryId];
    }


    /**
     * Get an array of query profiles (Zend_Db_Profiler_Query objects).  If $queryType
     * is set to one of the Zend_Db_Profiler::* constants then only queries of that
     * type will be returned.  Normally, queries that have not yet ended will
     * not be returned unless $showUnfinished is set to True.  If no
     * queries were found, False is returned.
     *
     * @param string $queryType
     * @param bool $showUnfinished
     * @return mixed
     */
    public function getQueryProfiles($queryType=null, $showUnfinished=false)
    {
        $queryProfiles = array();
        foreach ($this->_queryProfiles as $key=>$qp) {
            /* @var $qp Zend_Db_Profiler_Query */
            if ($queryType===null) {
                $condition=true;
            } else {
                $condition=($qp->getQueryType() & $queryType);
            }

            if (($qp->hasEnded() || $showUnfinished) && $condition) {
                $queryProfiles[$key] = $qp;
            }
        }

        if (empty($queryProfiles)) {
            $queryProfiles = false;
        }
        return $queryProfiles;
    }


    /**
     * Get the total elapsed time (in seconds) of all of the profiled queries.
     * Only queries that have ended will be counted.  If $queryType is set to
     * one of the Zend_Db_Profiler::* constants, the elapsed time will be calculated
     * only for queries of that type.
     *
     * @param int $queryType
     * @return int
     */
    public function getTotalElapsedSecs($queryType = null)
    {
        $elapsedSecs = 0;
        foreach ($this->_queryProfiles as $key=>$qp) {
            /* @var $qp Zend_Db_Profiler_Query */
            is_null($queryType)? $condition=true : $condition=($qp->getQueryType() & $queryType);
            if (($qp->hasEnded()) && $condition) {
                $elapsedSecs += $qp->getElapsedSecs();
            }
        }
        return $elapsedSecs;
    }


    /**
     * Get the total number of queries that have been profiled.  Only queries that have ended will
     * be counted.  If $queryType is set to one of the Zend_Db_Profiler::* constants, only queries of
     * that type will be counted.
     *
     * @param int $queryType
     * @return int
     */
    public function getTotalNumQueries($queryType = null)
    {
        if (is_null($queryType)) {
            return sizeof($this->_queryProfiles);
        }

        $numQueries = 0;
        foreach ($this->_queryProfiles as $qp) {
            /* @var $qp Zend_Db_Profiler_Query */
            is_null($queryType)? $condition=true : $condition=($qp->getQueryType() & $queryType);
            if ($qp->hasEnded() && $condition) {
                $numQueries++;
            }
        }
        return $numQueries;
    }


    /**
     * Get the Zend_Db_Profiler_Query object for the last query that was run, regardless if it has
     * ended or not.  If the query has not ended, it's end time will be Null.
     *
     * @return Zend_Db_Profiler_Query
     */
    public function getLastQueryProfile()
    {
        if (empty($this->_queryProfiles)) {
            return false;
        }

        end($this->_queryProfiles);
        return current($this->_queryProfiles);
    }

}

