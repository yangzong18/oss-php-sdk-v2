<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7;

final class Utils
{

    /**
     * Create a new stream based on the input type.
     *
     * Options is an associative array that can contain the following keys:
     * - metadata: Array of custom metadata.
     * - size: Size of the stream.
     */    
    public static function streamFor($resource = '', array $options = []): StreamInterface
    {
        return Psr7\Utils::streamFor($resource, $options);
    }

    /**
     * Determines the mimetype of a file by looking at its extension.
     */    
    public static function guessContentType(string $name, ?string $default = null): ?string
    {
        return Psr7\MimeType::fromFilename($name) ?? $default;
    }
}