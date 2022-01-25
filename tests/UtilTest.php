<?php

namespace Cascade\Tests;

use Cascade\Util;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * @author Deniz Dogan <deniz@dogan.se>
 */
class UtilTest extends TestCase
{
    public function testSnakeToCamelCase()
    {
        // non-strings
        $this->assertSame(null, Util::snakeToCamelCase(null));
        $this->assertSame(null, Util::snakeToCamelCase(array()));
        $this->assertSame(null, Util::snakeToCamelCase(1));

        // strings
        $this->assertSame('', Util::snakeToCamelCase(''));
        $this->assertSame('foo', Util::snakeToCamelCase('foo'));
        $this->assertSame('fooBar', Util::snakeToCamelCase('foo_bar'));
        $this->assertSame('fooBarBaz', Util::snakeToCamelCase('foo_bar_baz'));

        // weird strings
        $this->assertSame('_', Util::snakeToCamelCase('_'));
        $this->assertSame('__', Util::snakeToCamelCase('___'));
        $this->assertSame('_ _', Util::snakeToCamelCase('_ _'));
        $this->assertSame('x_', Util::snakeToCamelCase('X__'));
        $this->assertSame('_X', Util::snakeToCamelCase('__X'));
    }
}
