<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * Client used to interact with **Alibaba Cloud Object Storage Service (OSS)**.
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
        return $this->client->executeAsync($input, $options)->wait();
    }

    public function invokeOperationAsync(OperationInput $input, array $options = []): PromiseInterface
    {
        return $this->client->executeAsync($input, $options);
    }

    public function __call($name, $args): mixed
    {
        if (substr($name, -5) === 'Async') {
            $name = substr($name, 0, -5);
            $isAsync = true;
        }

        // api name
        $opName = ucfirst($name);
        $fromFunc = 'from' . $opName;
        $toFunc = 'to' . $opName;

        if (
            !\method_exists(Transform::class, $fromFunc) ||
            !\method_exists(Transform::class, $toFunc)
        ) {
            throw new \BadMethodCallException('Not implement ' . self::class . '::' . $name);
        }

        // args, {Operation}Request request, array options
        $request = isset($args[0]) ? $args[0] : [];
        $options = count($args) > 1 ? $args[1] : [];

        if (!($request instanceof Types\RequestModel)) {
            throw new \InvalidArgumentException('args[0] is not subclass of RequestModel, got ' . \gettype($request));
        }

        if (!\is_array($options)) {
            $options = [];
        }

        // execute
        $input = call_user_func([Transform::class, $fromFunc], $request);
        $promise = $this->client->executeAsync($input, $options)->then(
            function (OperationOutput $output) use ($toFunc) {
                return call_user_func([Transform::class, $toFunc], $output);
            }
        );

        // result
        return !empty($isAsync) ?  $promise : $promise->wait();
    }
}
