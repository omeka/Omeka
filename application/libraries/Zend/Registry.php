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
 * @package    Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Registry.php 2816 2007-01-16 01:42:51Z bkarwin $
 */


class Zend_Registry extends ArrayObject
{
    /**
     * getter method, basically same as offsetGet(), except if the key is not specified,
     * then the entire registry is returned (iterable).
     *
     * @param $index - OPTIONAL get the value associated with $index
     *
     * @return mixed
     */
    public function get($index=null)
    {
        if ($index === null) {
            return $this;
        }

        if (!$this->offsetExists($index)) {
           throw new Zend_Exception("No key named \"$index\" is registered.");
        }

        return $this->offsetGet($index);
    }
}
