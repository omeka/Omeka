<?php
use PHPUnit\Framework\TestCase;

/**
 * Compatibility wrapper to run same test cases on far-spread PHP and
 * PHPUnit versions.
 * 
 * 
 */
abstract class Omeka_Test_AbstractTestCase extends TestCase
{
    /**
     * getMock is deprecated/removed in PHPUnit 5+: if the new createMock
     * method is available, proxy to it, if not, we're on old PHPUnit, so
     * just call getMock.
     */
    public function getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false, $proxyTarget = null)
    {
        if (method_exists($this, 'createMock')) {
            return $this->createMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods, $proxyTarget);
        } else {
            return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods, $proxyTarget);
        }
    }

    public function setExpectedException($exceptionName, $exceptionMessage = '', $exceptionCode = null)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exceptionName);
            if ($exceptionMessage) {
                $this->expectExceptionMessage($exceptionMessage);
            }
            if ($exceptionCode) {
                $this->expectExceptionCode($exceptionCode);
            }
        } else {
            parent::setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
        }
    }

    /*
     * setUp, tearDown, etc. are defined with void return typehints in PHPUnit 8+.
     * To achieve compatiblity, we define these "Legacy" versions with no typehints
     * which are used by the actual test case classes. Different concrete extensions
     * of this class are used between different PHP versions (acting as a proxy for 
     * PHPUnit versions) to connect these to the PHPUnit base setUp, etc.
     */
    public function setUpLegacy()
    {
    }

    public function tearDownLegacy()
    {
    }

    public function assertPreConditionsLegacy()
    {
    }

    public function assertPostConditionsLegacy()
    {
    }

    public static function setUpBeforeClassLegacy()
    {
    }
}
