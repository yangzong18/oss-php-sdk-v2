<?php

namespace IntegrationTests;
require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestIntegration.php';

use AlibabaCloud\Oss\V2 as Oss;

class ClientTest extends TestIntegration
{
    public function testInvokeOperation()
    {
        $client = $this->getDefaultClient();
        $bucketName = self::randomBucketName();

        $input = new Oss\OperationInput(
            opName:'GetBucketAcl',
            method: 'GET',
            parameters: ['acl' => ''],
            bucket: $bucketName,
            opMetadata: ['sub-resource' => ['acl']]
        );

        try {
            $output = $client->invokeOperation($input);
        } catch (\Exception $e) {
            print $e;
        }
    }

    public function testGetBucketAcl()
    {
        $client = $this->getDefaultClient();
        $bucketName = self::randomBucketName();

        $result = $client->getBucketAcl(new Oss\Models\GetBucketAclRequest(
            bucket: $bucketName
        ));

        #print_r($result);

        $promise = $client->getBucketAclAsync(new Oss\Models\GetBucketAclRequest(
            bucket: $bucketName
        ));

        $result = $promise->wait();

        print_r($result);

    }

}