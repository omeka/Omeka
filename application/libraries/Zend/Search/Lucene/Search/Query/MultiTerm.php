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
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_Query */
require_once 'Zend/Search/Lucene/Search/Query.php';

/** Zend_Search_Lucene_Search_Weight_MultiTerm */
require_once 'Zend/Search/Lucene/Search/Weight/MultiTerm.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_MultiTerm extends Zend_Search_Lucene_Search_Query
{

    /**
     * Terms to find.
     * Array of Zend_Search_Lucene_Index_Term
     *
     * @var array
     */
    private $_terms = array();

    /**
     * Term signs.
     * If true then term is required.
     * If false then term is prohibited.
     * If null then term is neither prohibited, nor required
     *
     * If array is null then all terms are required
     *
     * @var array
     */
    private $_signs;

    /**
     * Result vector.
     * Bitset or array of document IDs
     * (depending from Bitset extension availability).
     *
     * @var mixed
     */
    private $_resVector = null;

    /**
     * Terms positions vectors.
     * Array of Arrays:
     * term1Id => (docId => array( pos1, pos2, ... ), ...)
     * term2Id => (docId => array( pos1, pos2, ... ), ...)
     *
     * @var array
     */
    private $_termsPositions = array();


    /**
     * A score factor based on the fraction of all query terms
     * that a document contains.
     * float for conjunction queries
     * array of float for non conjunction queries
     *
     * @var mixed
     */
    private $_coord = null;


    /**
     * Terms weights
     * array of Zend_Search_Lucene_Search_Weight
     *
     * @var array
     */
    private $_weights = array();


    /**
     * Class constructor.  Create a new multi-term query object.
     *
     * if $signs array is omitted then all terms are required
     * it differs from addTerm() behavior, but should never be used
     *
     * @param array $terms    Array of Zend_Search_Lucene_Index_Term objects
     * @param array $signs    Array of signs.  Sign is boolean|null.
     * @return void
     */
    public function __construct($terms = null, $signs = null)
    {
        if (is_array($terms)) {
            $this->_terms = $terms;

            $this->_signs = null;
            // Check if all terms are required
            if (is_array($signs)) {
                foreach ($signs as $sign ) {
                    if ($sign !== true) {
                        $this->_signs = $signs;
                        break;
                    }
                }
            }
        }
    }


    /**
     * Add a $term (Zend_Search_Lucene_Index_Term) to this query.
     *
     * The sign is specified as:
     *     TRUE  - term is required
     *     FALSE - term is prohibited
     *     NULL  - term is neither prohibited, nor required
     *
     * @param  Zend_Search_Lucene_Index_Term $term
     * @param  boolean|null $sign
     * @return void
     */
    public function addTerm(Zend_Search_Lucene_Index_Term $term, $sign = null) {
        if ($sign !== true || $this->_signs !== null) {       // Skip, if all terms are required
            if ($this->_signs === null) {                     // Check, If all previous terms are required
                foreach ($this->_terms as $prevTerm) {
                    $this->_signs[] = true;
                }
            }
            $this->_signs[] = $sign;
        }

        $this->_terms[] = $term;
    }


    /**
     * Re-write queries into primitive queries
     * Also used for query optimization and binding to the index
     *
     * @param Zend_Search_Lucene $index
     * @return Zend_Search_Lucene_Search_Query
     */
    public function rewrite(Zend_Search_Lucene $index)
    {
        if (count($this->_terms) == 0) {
            return new Zend_Search_Lucene_Search_Query_Empty();
        }

        // Check, that all fields are qualified
        $allQualified = true;
        foreach ($this->_terms as $term) {
            if ($term->field === null) {
                $allQualified = false;
                break;
            }
        }

        if ($allQualified) {
            return $this;
        } else {
            /** transform multiterm query to boolean and apply rewrite() method to subqueries. */
            $query = new Zend_Search_Lucene_Search_Query_Boolean();
            $query->setBoost($this->getBoost());

            foreach ($this->_terms as $termId => $term) {
                $subquery = new Zend_Search_Lucene_Search_Query_Term($term);

                $query->addSubquery($subquery->rewrite($index),
                                    ($this->_signs === null)?  true : $this->_signs[$subqueryId]);
            }

            return $query;
        }
    }

    /**
     * Returns query term
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->_terms;
    }


    /**
     * Return terms signs
     *
     * @return array
     */
    public function getSigns()
    {
        return $this->_signs;
    }


    /**
     * Set weight for specified term
     *
     * @param integer $num
     * @param Zend_Search_Lucene_Search_Weight_Term $weight
     */
    public function setWeight($num, $weight)
    {
        $this->_weights[$num] = $weight;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    public function createWeight($reader)
    {
        $this->_weight = new Zend_Search_Lucene_Search_Weight_MultiTerm($this, $reader);
        return $this->_weight;
    }


    /**
     * Calculate result vector for Conjunction query
     * (like '+something +another')
     *
     * @param Zend_Search_Lucene $reader
     */
    private function _calculateConjunctionResult($reader)
    {
        if (extension_loaded('bitset')) {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = bitset_from_array($reader->termDocs($term));
                } else {
                    $this->_resVector = bitset_intersection(
                                $this->_resVector,
                                bitset_from_array($reader->termDocs($term)) );
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        } else {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = array_flip($reader->termDocs($term));
                } else {
                    $termDocs = array_flip($reader->termDocs($term));
                    foreach($this->_resVector as $key=>$value) {
                        if (!isset( $termDocs[$key] )) {
                            unset( $this->_resVector[$key] );
                        }
                    }
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        }
    }


    /**
     * Calculate result vector for non Conjunction query
     * (like '+something -another')
     *
     * @param Zend_Search_Lucene $reader
     */
    private function _calculateNonConjunctionResult($reader)
    {
        if (extension_loaded('bitset')) {
            $required   = null;
            $neither    = bitset_empty();
            $prohibited = bitset_empty();

            foreach ($this->_terms as $termId => $term) {
                $termDocs = bitset_from_array($reader->termDocs($term));

                if ($this->_signs[$termId] === true) {
                    // required
                    if ($required !== null) {
                        $required = bitset_intersection($required, $termDocs);
                    } else {
                        $required = $termDocs;
                    }
                } elseif ($this->_signs[$termId] === false) {
                    // prohibited
                    $prohibited = bitset_union($prohibited, $termDocs);
                } else {
                    // neither required, nor prohibited
                    $neither = bitset_union($neither, $termDocs);
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }

            if ($required === null) {
                $required = $neither;
            }
            $this->_resVector = bitset_intersection( $required,
                                                     bitset_invert($prohibited, $reader->count()) );
        } else {
            $required   = null;
            $neither    = array();
            $prohibited = array();

            foreach ($this->_terms as $termId => $term) {
                $termDocs = array_flip($reader->termDocs($term));

                if ($this->_signs[$termId] === true) {
                    // required
                    if ($required !== null) {
                        // substitute for bitset_intersection
                        foreach ($required as $key => $value) {
                            if (!isset( $termDocs[$key] )) {
                                unset($required[$key]);
                            }
                        }
                    } else {
                        $required = $termDocs;
                    }
                } elseif ($this->_signs[$termId] === false) {
                    // prohibited
                    // substitute for bitset_union
                    foreach ($termDocs as $key => $value) {
                        $prohibited[$key] = $value;
                    }
                } else {
                    // neither required, nor prohibited
                    // substitute for bitset_union
                    foreach ($termDocs as $key => $value) {
                        $neither[$key] = $value;
                    }
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }

            if ($required === null) {
                $required = $neither;
            }

            foreach ($required as $key=>$value) {
                if (isset( $prohibited[$key] )) {
                    unset($required[$key]);
                }
            }
            $this->_resVector = $required;
        }
    }


    /**
     * Score calculator for conjunction queries (all terms are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _conjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = $reader->getSimilarity()->coord(count($this->_terms),
                                                            count($this->_terms) );
        }

        $score = 0.0;

        foreach ($this->_terms as $termId=>$term) {
            $score += $reader->getSimilarity()->tf(count($this->_termsPositions[$termId][$docId]) ) *
                      $this->_weights[$termId]->getValue() *
                      $reader->norm($docId, $term->field);
        }

        return $score * $this->_coord * $this->getBoost();
    }


    /**
     * Score calculator for non conjunction queries (not all terms are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _nonConjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = array();

            $maxCoord = 0;
            foreach ($this->_signs as $sign) {
                if ($sign !== false /* not prohibited */) {
                    $maxCoord++;
                }
            }

            for ($count = 0; $count <= $maxCoord; $count++) {
                $this->_coord[$count] = $reader->getSimilarity()->coord($count, $maxCoord);
            }
        }

        $score = 0.0;
        $matchedTerms = 0;
        foreach ($this->_terms as $termId=>$term) {
            // Check if term is
            if ($this->_signs[$termId] !== false &&            // not prohibited
                isset($this->_termsPositions[$termId][$docId]) // matched
               ) {
                $matchedTerms++;
                $score +=
                      $reader->getSimilarity()->tf(count($this->_termsPositions[$termId][$docId]) ) *
                      $this->_weights[$termId]->getValue() *
                      $reader->norm($docId, $term->field);
            }
        }

        return $score * $this->_coord[$matchedTerms] * $this->getBoost();
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function score($docId, $reader)
    {
        if($this->_resVector === null) {
            if ($this->_signs === null) {
                $this->_calculateConjunctionResult($reader);
            } else {
                $this->_calculateNonConjunctionResult($reader);
            }

            $this->_initWeight($reader);
        }

        if ( (extension_loaded('bitset')) ?
                bitset_in($this->_resVector, $docId) :
                isset($this->_resVector[$docId])  ) {
            if ($this->_signs === null) {
                return $this->_conjunctionScore($docId, $reader);
            } else {
                return $this->_nonConjunctionScore($docId, $reader);
            }
        } else {
            return 0;
        }
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only for query visualisation, so we don't care about characters escaping

        $query = '';

        foreach ($this->_terms as $id => $term) {
            if ($id != 0) {
                $query .= ' ';
            }

            if ($this->_signs === null || $this->_signs[$id] === true) {
                $query .= '+';
            } else if ($this->_signs[$id] === false) {
                $query .= '-';
            }

            if ($term->field !== null) {
                $query .= $term->field . ':';
            }
            $query .= $term->text;
        }

        return $query;
    }
}

