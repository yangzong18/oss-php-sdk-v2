<?php

if (is_file(__DIR__ . '/autoload.php')) {
    require_once __DIR__ . '/autoload.php';
}
if (is_file(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use OSS\Exception\OssException;
use OSS\OssClient;
use OSS\Credentials\StaticCredentialsProvider;
use OSS\Signer\Signer;

$credentials = new StaticCredentialsProvider("LTAI5t78iFoXdK*******","h2y6CviDsJiAs3XBrx*******");
$ossClient = new OssClient([
    'region'          => 'oss-cn-hangzhou',
    'endpoint'    => 'http://oss-cn-hangzhou.aliyuncs.com',
    'provider' => $credentials
]);

$bucketName = "sample-bucket";
$key = 'test-key.txt';

try {
    $f = fopen('demo.php','r');
    $result = $ossClient->putObject(array('bucket'=>$bucketName,'key'=>$key,'body'=>$f,'method'=>'put'));
    if (is_resource($f)) {
        fclose($f);
    }
    print_r($result);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->getObject(array('bucket'=>$bucketName,'key'=>'demo.txt','method'=>"GET"));
    print_r(strval($result['body']));
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->doesObjectExist($bucketName,$key);
    var_export($result);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $headers = ['x-oss-acl'=> 'public-read'];
    $result = $ossClient->putBucketAcl(array('bucket'=>$bucketName,'method'=>'put','headers'=>$headers,'parameters'=>['acl'=>'']));
    print_r($result);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->getBucketAcl(array('bucket'=>$bucketName,'method'=>'get','parameters'=>['acl'=>'']));
    print_r($result['body']['AccessControlList']['Grant']);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->doesObjectExist($bucketName,$key);
    var_export($result);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->getObjectAcl(array('bucket'=>$bucketName,'key'=>$key,'method'=>'get','parameters'=>['acl'=>'']));
    print_r($result['body']['AccessControlList']['Grant']);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $headers = ['x-oss-acl'=> 'public-read','x-oss-version-id'=>'CAEQFxiBgICq_riyuRgiIGVkY2FmMGEx****'];
    $result = $ossClient->putObjectAcl(array('bucket'=>$bucketName,'key'=>$key,'method'=>'get','headers'=>$headers,'parameters'=>['acl'=>'']));
    print_r($result);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->getBucketResourceGroup(array('bucket'=>$bucketName,'method'=>'get','parameters'=>['resourceGroup'=>''],'metadata'=>[Signer::SUB_RESOURCE=>['resourceGroup']]));
    print($result['body']['ResourceGroupId']);
}catch (OssException $e){
    printf($e->getMessage());
}

try {
    $result = $ossClient->doesBucketExist($bucketName);
    var_export($result);
}catch (OssException $e){
    printf($e->getMessage());
}

