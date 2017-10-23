<?php

namespace Cascade\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function getMock($originalClassName, $methods = [], array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false, $proxyTarget = null)
    {
        $phpUnitVersion = \PHPUnit_Runner_Version::id();

        if (version_compare($phpUnitVersion, '5.7.0', 'lt')) {
            return parent::getMock(
                $originalClassName,
                $methods,
                $arguments,
                $mockClassName,
                $callOriginalConstructor,
                $callOriginalClone,
                $callAutoload,
                $cloneArguments,
                $callOriginalMethods,
                $proxyTarget
            );
        }
        return $this->createMock($originalClassName);
    }
}
