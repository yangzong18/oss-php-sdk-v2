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

    public static function safetyBool(mixed $value): bool
    {
        if (($value === true) || ($value === 'true')) {
            return true;
        }
        return false;
    }

    public static function safetyString(mixed $value): string
    {
        if ((string)$value === $value) {
            return $value;
        }
        return '';
    }

    public static function safetyFloat(mixed $value): float
    {
        if ((float)$value === $value) {
            return $value;
        }
        return 0;
    }

    public static function addScheme(string $endpoint, bool $disableSsl): string
    {
        return $endpoint;
    }

    public static function regionToEndpoint(string $region, bool $disableSsl, string $type): string
    {
        return '';
    }

    public static function defaultUserAgent(): string
    {
        return 'alibabacloud-php-sdk-v2/1.0.0-dev' . " (" . php_uname('s') . "/" . php_uname('r') . "/" . php_uname('m') . ";" . PHP_VERSION . ")";
    }

    /**
     * URL encode
     */
    public static function urlEncode(string $key, bool $ignore = false): string
    {
        $value = rawurlencode($key);
        if ($ignore) {
            return str_replace('%2F', '/', $value);
        }
        return $value;
    }
}
