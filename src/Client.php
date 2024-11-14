<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use GuzzleHttp\Promise\PromiseInterface;

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
}
