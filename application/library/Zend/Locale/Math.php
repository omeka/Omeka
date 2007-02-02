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
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Math.php 2883 2007-01-18 05:56:31Z gavin $
 */


/**
 * Utility class for proxying math function to bcmath functions, if present,
 * otherwise to PHP builtin math operators, with limited detection of overflow conditions.
 * Sampling of PHP environments and platforms suggests that at least 80% to 90% support bcmath.
 * Thus, this file should be as light as possible.
 *
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Locale_Math
{
    // support unit testing without using bcmath functions 
    static protected $_bcmathDisabled = false;

    static public $add   = 'bcadd';
    static public $sub   = 'bcsub';
    static public $pow   = 'bcpow';
    static public $mul   = 'bcmul';
    static public $div   = 'bcdiv';
    static public $comp  = 'bccomp';
    static public $sqrt  = 'bcsqrt';
    static public $mod   = 'bcmod';
    static public $scale = 'bcscale';

    static public function isBcmathDisabled()
    {
        return self::$_bcmathDisabled;
    }
}

if ((defined('TESTS_ZEND_LOCALE_BCMATH_ENABLED') && !TESTS_ZEND_LOCALE_BCMATH_ENABLED)
    || !extension_loaded('bcmath')) {
    require_once 'Zend/Locale/Math/PhpMath.php';
}

?>
