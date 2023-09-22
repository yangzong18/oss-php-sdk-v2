<?php

namespace OSS;

use GuzzleHttp\Promise\Promise;
use OSS\Exception\OssException;
use OSS\Utils\OssUtil;

/**
 * Trait OssClientTrait
 * @package OSS
 */
trait OssClientTrait{
    /**
     * Check object exist or no exist
     * @param $bucket
     * @param $key
     * @param array $options
     * @return bool
     */
    public function doesObjectExist($bucket, $key, array $options = []){
        try {
            $this->headObject([
                    'bucket' => $bucket,
                    'key'    => $key,
                    'method' => 'HEAD'
                ] + $options);
            return true;
        } catch (OssException $e) {
            if ($e->getErrorCode() == 'AccessDenied') {
                return true;
            }
            if ($e->getStatusCode() >= 500) {
                throw $e;
            }
            return false;
        }
    }

    /**
     * Check bucket exist or no exist
     * @param $bucket
     * @return bool
     */
    public function doesBucketExist($bucket)
    {
        try {
            $this->headBucket([
                    'bucket' => $bucket,
                    'method' => 'HEAD'
                ]);
            return true;
        } catch (OssException $e) {
            if ($e->getErrorCode() == 'AccessDenied') {
                return true;
            }
            if ($e->getStatusCode() >= 500) {
                throw $e;
            }
            return false;
        }
    }

    /**
     * Get Object Meta Data
     * @param $bucket
     * @param $key
     * @param array $options
     * @return bool
     */
    public function getObjectMeta($bucket, $key, array $options = []){
        try {
            return $this->headObject([
                    'bucket' => $bucket,
                    'key'    => $key,
                    'method' => 'HEAD',
                    'parameters'=>['objectMeta'=>'']
                ] + $options);
        } catch (OssException $e) {
            throw $e;
        }
    }

    /**
     * List Objects V2
     * @param array $args
     * @return Result
     */
    public function listObjectsV2(array $args = [])
    {
        return $this->listObjects(array_replace_recursive([
            'method' => 'get',
            'parameters'=>['list-type'=>2]
        ], $args));
    }

    /**
     * List Objects V2 Async
     * @param array $args
     * @return Promise
     */
    public function listObjectsV2Async(array $args = [])
    {
        return $this->listObjectsAsync(array_replace_recursive([
            'method' => 'get',
            'parameters'=>['list-type'=>2]
        ], $args));
    }


    /**
     * Result Paginator
     * @param $name
     * @param array $args
     * @return ResultPaginator
     */
    public function getPaginator($name, array $args = [])
    {
        $config = OssUtil::getPaginatorConfig($name);
        return new ResultPaginator($this, $name, $args, $config);
    }
}