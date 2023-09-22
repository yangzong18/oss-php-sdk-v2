<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
require_once __DIR__ . '/Config.php';

use OSS\Credentials\EnvironmentVariableCredentialsProvider;
use OSS\OssClient;
use OSS\Exception\OssException;


class Common{
    /**
     * According to the Config configuration, get an OssClient instance
     *
     * @return OssClient  An OssClient instance
     */
    public static function getOssClient()
    {
        try {
            $credentials = new EnvironmentVariableCredentialsProvider();
            $ossClient = new OssClient([
                'region'          => self::getRegion(),
                'endpoint'    => self::getEndpoint(),
                'provider' => $credentials
            ]);
            return $ossClient;
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
    }

    public static function getBucketName()
    {
        return getenv('OSS_BUCKET');
    }

    public static function getRegion()
    {
        return getenv('OSS_REGION');
    }

    public static function getEndpoint()
    {
        return getenv('OSS_ENDPOINT');
    }

    public static function getCallbackUrl()
    {
        return getenv('OSS_CALLBACK_URL');
    }

    public static function getAccessKeyId()
    {
        return getenv('OSS_ACCESS_KEY_ID');
    }

    public static function getAccessKeySecret()
    {
        return getenv('OSS_ACCESS_KEY_SECRET');
    }
}
