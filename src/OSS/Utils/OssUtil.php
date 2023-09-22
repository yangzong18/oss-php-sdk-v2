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
class OssUtil{
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
        $node = self::getXmlNode($rootXml);
        self::appendToXml($data[$rootXml],$xml,$node);
        return $xml->asXML();
    }


    /**
     * Append into Xml
     * @param array $array
     * @param \SimpleXMLElement $xml
     * @param string $node
     */
    public static function appendToXml($array,&$xml,$node)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (in_array($key,$node)) {
                    if (is_array($value[0])){
                        foreach ($value as $subArray) {
                            $subNode = $xml->addChild($key);
                            self::appendToXml($subArray, $subNode,$node);
                        }
                    }else{
                        if (isset($value[0])){
                            foreach ($value as $val){
                                $xml->addChild($key,$val);
                            }
                        }else{
                            if (!is_numeric($key)){
                                $subNode = $xml->addChild($key);
                                self::appendToXml($value, $subNode,$node);
                            }else{
                                self::appendToXml($value, $xml,$node);
                            }
                        }
                    }
                } else {
                    if (!is_numeric($key)){
                        $subNode = $xml->addChild($key);
                        self::appendToXml($value, $subNode,$node);
                    }else{
                        self::appendToXml($value, $xml,$node);
                    }
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

    public static function getXmlNode($key){
        $xmlNodeArray = [
            'LifecycleConfiguration'=> ['Rule','Transition'],
            'ReplicationConfiguration' => ['Prefix'],
            'InventoryConfiguration' => ['Field'],
            'WebsiteConfiguration' => ['Pass','Remove'],
            'RefererConfiguration' => ['Referer'],
            'Tagging' => ['Tag'],
            'CORSConfiguration'=>['CORSRule','AllowedOrigin','AllowedMethod','AllowedHeader','ExposeHeader'],
            'MetaQuery' => ['Aggregation'],
            'AntiDDOSConfiguration' => ['Domain'],
            'TlsVersionConfiguration' => ['TLSVersion']
        ];

        return isset($xmlNodeArray[$key]) ? $xmlNodeArray[$key] : [];
    }


    public static function getInterfaceKey($key){
        $keyArray = [
            'listBuckets' => [['Buckets','Bucket']],
            'listMultipartUploads' => [['Upload']],
            'listObjectVersions' =>[['Version']],
            'listObjects'=> [['Contents']],
            'listObjectsV2'=> [['Contents']],
            'listParts' => [['Part']],
        ];
        return isset($keyArray[$key]) ? $keyArray[$key] : [];
    }


    /**
     * Paginator Config
     * @param string $name
     * @return mixed
     */
    public static function getPaginatorConfig($name){
        $config = [
            'listBuckets' => ['input_token'=>'marker','limit_key'=>'max-keys','more_results'=>'IsTruncated','output_token'=>'NextMarker','result_key'=>'Bucket'],
            'listMultipartUploads' => ['input_token'=>['key-marker','upload-id-marker'],'limit_key'=>'max-uploads','more_results'=>'IsTruncated','output_token'=>['NextKeyMarker','NextUploadIdMarker'],'result_key'=>'Upload'],
            'listObjectVersions' =>['input_token'=>['key-marker','version-id-marker'],'limit_key'=>'max-keys','more_results'=>'IsTruncated','output_token'=>['NextKeyMarker','NextVersionIdMarker'],'result_key'=>'Version'],
            'listObjects'=> ['input_token'=>'marker','limit_key'=>'max-keys','more_results'=>'IsTruncated','output_token'=>'NextMarker','result_key'=>'Contents'],
            'listObjectsV2'=> ['input_token'=>'continuation-token','limit_key'=>'max-keys','more_results'=>'IsTruncated','output_token'=>'NextContinuationToken','result_key'=>'Contents'],
            'listParts' => ['input_token'=>'part-number-marker','limit_key'=>'max-parts','more_results'=>'IsTruncated','output_token'=>'NextPartNumberMarker','result_key'=>'Part'],

        ];
        if (isset($config[$name])){
            return $config[$name];
        }else{
            throw new OssException('Paging config does not exist.');
        }
    }

    /**
     * Applies a map function $f to each value in a collection.
     * @param $iterable
     * @param callable $f
     * @return \Generator
     */
    public static function map($iterable, callable $f)
    {
        foreach ($iterable as $value) {
            yield $f($value);
        }
    }

    /**
     * Creates a generator that iterates over a sequence, then iterates over each
     * value in the sequence and yields the application of the map function to each
     * value.
     * @param $iterable
     * @param callable $f
     * @return \Generator
     */
    public static function flatmap($iterable, callable $f)
    {
        foreach (self::map($iterable,$f) as $outer) {
            foreach ($outer as $inner) {
                yield $inner;
            }
        }
    }

    /**
     * Replace Array
     * @param array $array
     * @param array $keys
     */
    public static function replaceBodyArray(&$array, $keys) {
        $result =&$array;
        $fool = false;
        foreach ($keys as $key) {
            if (isset($result[$key]) && is_array($result[$key])) {
                $fool = true;
                $result = &$result[$key];
            }
        }
        if (!is_numeric(array_key_first($result)) && $fool){
            $result = array(0 => $result);
        }
    }
}
