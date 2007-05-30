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
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Analysis_Analyzer_Common */
require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum extends Zend_Search_Lucene_Analysis_Analyzer_Common
{
    private $_position;

    /**
     * Reset token stream
     */
    public function reset()
    {
        $this->_position = 0;
    }

    /**
     * Tokenization stream API
     * Get next token
     * Returns null at the end of stream
     *
     * @return Zend_Search_Lucene_Analysis_Token|null
     */
    public function nextToken()
    {
        if ($this->_input === null) {
            return null;
        }

        while ($this->_position < strlen($this->_input)) {
            // skip white space
            while ($this->_position < strlen($this->_input) &&
                   !ctype_alnum( $this->_input[$this->_position] )) {
                $this->_position++;
            }

            $termStartPosition = $this->_position;

            // read token
            while ($this->_position < strlen($this->_input) &&
                   ctype_alnum( $this->_input[$this->_position] )) {
                $this->_position++;
            }

            // Empty token, end of stream.
            if ($this->_position == $termStartPosition) {
                return null;
            }

            $token = new Zend_Search_Lucene_Analysis_Token(
                                      substr($this->_input,
                                             $termStartPosition,
                                             $this->_position - $termStartPosition),
                                      $termStartPosition,
                                      $this->_position);
            $token = $this->normalize($token);
            if ($token !== null) {
                return $token;
            }
            // Continue if token is skipped
        }

        return null;
    }
}

