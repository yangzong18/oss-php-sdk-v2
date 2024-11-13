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

    public function testGuessContentType()
    {
        $this->assertEquals(null, Utils::guessContentType(''));
        $this->assertEquals('application/octet-stream', Utils::guessContentType('','application/octet-stream'));
        $this->assertEquals('image/jpeg', Utils::guessContentType('1.jpeg'));
        $this->assertEquals('image/jpeg', Utils::guessContentType('1.jpeg','application/octet-stream'));
    }
}