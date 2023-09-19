<?php

namespace OSS;

use OSS\Exception\OssException;

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
}