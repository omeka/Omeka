<?php
use PHPUnit\Framework\TestCase;

class Omeka_Test_TestCase extends TestCase
{
    /**
     * Backward compatibility wrapper for tests written against old PHPUnit
     * 
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
}
