<?php

namespace UnitTests;

use Psr\Http\Message\StreamInterface;
use AlibabaCloud\Oss\V2\Utils;

class UtilsTest extends \PHPUnit\Framework\TestCase
{
    public function testStreamFor()
    {
        $data = 'hello world';
        $stream = Utils::streamFor($data);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals(11, $stream->getSize());
        $this->assertEquals(true, $stream->isSeekable());
        $got = $stream->read(20);
        $this->assertEquals($data, $got);

        $got = $stream->read(20);
        $this->assertEquals('', $got);

        $stream->rewind();
        $got = $stream->read(20);
        $this->assertEquals($data, $got);
    }

    public function testGuessContentType(): void
    {
        $this->assertEquals(null, Utils::guessContentType(''));
        $this->assertEquals('application/octet-stream', Utils::guessContentType('', 'application/octet-stream'));
        $this->assertEquals('image/jpeg', Utils::guessContentType('1.jpeg'));
        $this->assertEquals('image/jpeg', Utils::guessContentType('1.jpeg', 'application/octet-stream'));
    }

    public function testUrlEncode(): void
    {
        $this->assertEquals('123%2F123%2F%2B%20%3F%2F123', Utils::urlEncode('123/123/+ ?/123'));
        $this->assertEquals('123/123/%2B%20%3F/123', Utils::urlEncode('123/123/+ ?/123', true));
    }

    public function testIsIpFormat(): void
    {
        $this->assertTrue(Utils::isIPFormat("10.101.160.147"));
        $this->assertTrue(Utils::isIPFormat("12.12.12.34"));
        $this->assertTrue(Utils::isIPFormat("12.12.12.12"));
        $this->assertTrue(Utils::isIPFormat("255.255.255.255"));
        $this->assertTrue(Utils::isIPFormat("0.1.1.1"));
        $this->assertFalse(Utils::isIPFormat("0.1.1.x"));
        $this->assertFalse(Utils::isIPFormat("0.1.1.256"));
        $this->assertFalse(Utils::isIPFormat("256.1.1.1"));
        $this->assertFalse(Utils::isIPFormat("0.1.1.0.1"));
        $this->assertTrue(Utils::isIPFormat("10.10.10.10:123"));
    }

    public function testToSimpleArray(): void
    {
        $this->assertEquals([], Utils::toSimpleArray([]));
        $this->assertEquals([1, 2, 3], Utils::toSimpleArray([1, 2, 3]));
        $this->assertEquals(
            ['1' => '1-1', '2' => '2-1', '3' => '3-1'],
            Utils::toSimpleArray(['1' => ['1-1', '1-2'], '2' => ['2-1', '2-2'], '3' => '3-1'])
        );
    }

    public function testCalcContentMd5(): void
    {
        $this->assertEquals('1B2M2Y8AsgTpgAmY7PhCfg==', Utils::calcContentMd5(Utils::streamFor('')));
        $body = Utils::streamFor("hello world\n");
        $this->assertEquals('b1kCrCNwJL3QwXbLkwY9xA==', Utils::calcContentMd5($body));
        $this->assertEquals('b1kCrCNwJL3QwXbLkwY9xA==', Utils::calcContentMd5($body));
    }
}
