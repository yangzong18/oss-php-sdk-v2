<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Object Storage Service(OSS)'s client class, which wraps all OSS APIs user could call to talk to OSS.
 * Users could do operations on bucket, object, including MultipartUpload or setting ACL via an OSSClient instance.
 * For more details, please check out the OSS API document:https://www.alibabacloud.com/help/doc-detail/31947.htm
 *
 * @method Models\GetBucketAclResult getBucketAcl(Models\GetBucketAclRequest $request, array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketAclAsync(Models\GetBucketAclRequest $request, array $args = [])
 */
final class Client
{
    private ClientImpl $client;

    public function __construct(Config $config, array $options = [])
    {
        $this->client = new ClientImpl($config, $options);
    }

    public function invokeOperation(OperationInput $input, array $options = []): OperationOutput
    {
        return $this->client->invokeOperationAsync($input, $options)->wait();
    }

    public function invokeOperationAsync(OperationInput $input, array $options = []): PromiseInterface
    {
        return $this->client->invokeOperationAsync($input, $options);
    }

    public function __call($name, $args)
    {
        if (substr($name, -5) === 'Async') {
            $name = substr($name, 0, -5);
            $isAsync = true;
        }

        #var_dump($args);

        // args, {Operation}Request request, array options
        $request = isset($args[0])? $args[0]: [];
        $options = count($args) > 1 ? $args[1] : [];
        $opName = ucfirst($name);

        if (!($request instanceof Types\RequestModel)) {
            throw new \InvalidArgumentException('args[0] is not subclass of RequestModel, got ' . \gettype($request));
        }

        if (!\is_array($options)) {
            $options = [];
        }

        $input = call_user_func([Transform::class, 'from' . $opName], $request);

        // execute
        $promise = $this->client->invokeOperationAsync($input, $options)->then(
            function (OperationOutput $output) use ($opName) {
                return call_user_func([Transform::class, 'to' . $opName], $output);
            }
        );

        return !empty($isAsync) ?  $promise : $promise->wait();
    }
}
