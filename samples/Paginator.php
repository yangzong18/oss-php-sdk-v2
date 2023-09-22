<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
require_once __DIR__ . '/Common.php';

use OSS\Exception\OssException;
use OSS\OssClient;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* For complete usage, see the following functions ****************************************************

listBucketsPaginator($ossClient);
listObjectsPaginator($ossClient,$bucket);
listObjectsV2Paginator($ossClient,$bucket);
listObjectVersionsPaginator($ossClient,$bucket);
listMultipartUploadsPaginator($ossClient,$bucket);
listPartsPaginator($ossClient,$bucket);

/**
 * ListBuckets sample in Painter
 * @param OssClient $ossClient
 */
function listBucketsPaginator($ossClient){
    try {
        $result = $ossClient->getPaginator('listBuckets',['method'=>'get','parameters'=>['max-keys'=>1]]);
        foreach ($result->search('Buckets.Bucket[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}


/**
 * ListObjects sample in Painter
 * @param OssClient$ossClient
 * @param string $bucket bucket name
 */
function listObjectsPaginator($ossClient,$bucket){
    try {
        $result = $ossClient->getPaginator('listObjects',['bucket'=>$bucket,'method'=>'get','parameters'=>['max-keys'=>50]]);
        foreach ($result->search('Contents[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}

/**
 * ListObjectsV2 sample in Painter
 * @param OssClient$ossClient
 * @param string $bucket bucket name
 */
function listObjectsV2Paginator($ossClient,$bucket){
    try {
        $result = $ossClient->getPaginator('listObjectsV2',['bucket'=>$bucket,'method'=>'get','parameters'=>['max-keys'=>50]]);
        foreach ($result->search('Contents[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}

/**
 * listObjectVersions sample in Painter
 * @param OssClient$ossClient
 * @param string $bucket bucket name
 */
function listObjectVersionsPaginator($ossClient,$bucket){
    try {
        $result = $ossClient->getPaginator('listObjectVersions',['bucket'=>$bucket,'method'=>'get','parameters'=>['max-keys'=>50,'versions'=>'']]);
        foreach ($result->search('Version[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}

/**
 * ListMultipartUploads sample in Painter
 * @param OssClient$ossClient
 * @param string $bucket bucket name
 */
function listMultipartUploadsPaginator($ossClient,$bucket){
    try {
        $result = $ossClient->getPaginator('listMultipartUploads',['bucket'=>$bucket,'method'=>'get','parameters'=>['max-keys'=>50,'uploads'=>'']]);
        foreach ($result->search('Upload[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}

/**
 * ListMultipartUploads sample in Painter
 * @param OssClient$ossClient
 * @param string $bucket bucket name
 */
function listPartsPaginator($ossClient,$bucket){
    try {
        $result = $ossClient->getPaginator('listParts',['bucket'=>$bucket,'key'=>'test.mp4','method'=>'get','parameters'=>['max-parts'=>10,'uploadId'=>'27FC262691854D41BDDD40F842BD1C2B']]);
        foreach ($result->search('Part[]') as $item){
            print_r($item);
        }
    }catch (OssException $e){
        printf($e->getMessage());
    }
}




