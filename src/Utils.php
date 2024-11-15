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

    /**
     * Calculate a content-md5 of a stream.
     */
    public static function calcContentMd5(StreamInterface $stream): string
    {
        return base64_encode(Psr7\Utils::hash($stream, 'md5', true));
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

    /**
     * Check if the endpoint is in the IPv4 format, such as xxx.xxx.xxx.xxx:port or xxx.xxx.xxx.xxx.
     */
    public static function isIPFormat($endpoint): bool
    {
        $ip_array = explode(":", $endpoint);
        $hostname = $ip_array[0];
        $ret = filter_var($hostname, \FILTER_VALIDATE_IP);
        if (!$ret) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * change array[string => string[]] to array[string => string]
     */
    public static function toSimpleArray(array $value): array
    {
        $result = [];
        foreach ($value as $k => $vv) {
            if (\is_array($vv)) {
                $result[$k] =  $vv[0];
            } else {
                $result[$k] =  $vv;
            }
        }
        return  $result;
    }
}
