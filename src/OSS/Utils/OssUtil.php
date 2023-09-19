<?php

namespace OSS\Utils;

use OSS\Exception\OssException;

/**
 * Class OssUtil
 *
 * Oss Util class for OssClient. The caller could use it for formating the result from OssClient.
 *
 * @package OSS
 */
class OssUtil
{
    const OSS_CONTENT = 'content';
    const OSS_LENGTH = 'length';
    const OSS_HEADERS = 'headers';
    const OSS_MAX_OBJECT_GROUP_VALUE = 1000;
    const OSS_MAX_PART_SIZE = 5368709120; // 5GB
    const OSS_MID_PART_SIZE = 10485760; // 10MB
    const OSS_MIN_PART_SIZE = 102400; // 100KB
    /**
     * Checks if the bucket name is valid
     * bucket naming rules
     * 1. Can only include lowercase letters, numbers, or dashes
     * 2. Must start and end with lowercase letters or numbers
     * 3. Must be within a length from 3 to 63 bytes.
     *
     * @param string $bucket Bucket name
     * @return boolean
     */
    public static function validateBucket($bucket)
    {
        $pattern = '/^[a-z0-9][a-z0-9-]{2,62}$/';
        if (!preg_match($pattern, $bucket)) {
            return false;
        }
        return true;
    }

    /**
     * Checks if object name is valid
     * object naming rules:
     * 1. Must be within a length from 1 to 1023 bytes
     * 2. Cannot start with '/' or '\\'.
     * 3. Must be encoded in UTF-8.
     *
     * @param string $object Object名称
     * @return boolean
     */
    public static function validateObject($object)
    {
        $pattern = '/^.{1,1023}$/';
        if (!preg_match($pattern, $object) ||
            self::startsWith($object, '/') || self::startsWith($object, '\\')
        ) {
            return false;
        }
        return true;
    }


    /**
     * Checks if $str starts with $findMe
     *
     * @param string $str
     * @param string $findMe
     * @return bool
     */
    public static function startsWith($str, $findMe)
    {
        if (strpos($str, $findMe) === 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Generate the xml message of createBucketXmlBody.
     *
     * @param string $storageClass
     * @return string
     */
    public static function createBucketXmlBody($storageClass)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><CreateBucketConfiguration></CreateBucketConfiguration>');
        $xml->addChild('StorageClass',  $storageClass);
        return $xml->asXML();
    }

    /**
     * validate $options
     *
     * @param array $options
     * @throws OssException
     * @return boolean
     */
    public static function validateOptions($options)
    {
        //$options
        if ($options != NULL && !is_array($options)) {
            throw new OssException ($options . ':' . 'option must be array');
        }
    }

    /**
     * check whether the Content is valid.
     *
     * @param $content string
     * @throws OssException
     */
    public static function validateContent($content)
    {
        if (empty($content)) {
            throw new OssException("http body content is invalid");
        }
    }


    /**
     * Check if the endpoint is in the IPv4 format, such as xxx.xxx.xxx.xxx:port or xxx.xxx.xxx.xxx.
     *
     * @param string $endpoint The endpoint to check.
     * @return boolean
     */
    public static function isIPFormat($endpoint)
    {
        $ip_array = explode(":", $endpoint);
        $hostname = $ip_array[0];
        $ret = filter_var($hostname, FILTER_VALIDATE_IP);
        if (!$ret) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the host:port from endpoint.
     *
     * @param string $endpoint the endpoint.
     * @return boolean
     */
    public static function getHostPortFromEndpoint($endpoint)
    {
        $str = $endpoint;
        $pos = strpos($str, "://");
        if ($pos !== false) {
            $str = substr($str, $pos+3);
        }
    
        $pos = strpos($str, '#');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }
    
        $pos = strpos($str, '?');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }
    
        $pos = strpos($str, '/');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }
    
        $pos = strpos($str, '@');
        if ($pos !== false) {
            $str = substr($str, $pos+1);
        }
       
        if (!preg_match('/^[\w.-]+(:[0-9]+)?$/', $str)) {
            throw new OssException("endpoint is invalid:" . $endpoint);
        }

        return $str;
    }


    /**
     * Change array to xml string
     * @param $data
     * @return bool|string
     * @throws \Exception
     */
    public static function arrayToXml($data)
    {
        $rootXml = array_key_first($data);
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><'. $rootXml . '></'. $rootXml .'>');
        self::appendToXml($data[$rootXml],$xml);
        return $xml->asXML();
    }

    public static function appendToXml($array,&$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subNode = $xml->addChild($key);
                    self::appendToXml($value, $subNode);
                } else {
                    self::appendToXml($value, $xml);
                }
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * @return string[]
     */
    public static function getRequiredParams(){
        return array(
            "acl" => "",
            "bucketInfo" => "",
            "location" => "",
            "stat" => "",
            "delete" => "",
            "append" => "",
            "tagging" => "",
            "objectMeta" => "",
            "uploads" => "",
            "uploadId" => "",
            "partNumber" => "",
            "security-token" => "",
            "position" => "",
            "response-content-type" => "",
            "response-content-language" => "",
            "response-expires" => "",
            "response-cache-control" => "",
            "response-content-disposition" => "",
            "response-content-encoding" => "",
            "restore" => "",
            "callback" => "",
            "callback-var" => "",
            "versions" => "",
            "versioning" => "",
            "versionId" => "",
            "sequential" => "",
            "continuation-token" => "",
            "regionList" => "",
            "cloudboxes" => "",
        );
    }
}
