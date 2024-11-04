<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Retry;


interface BackoffDelayerInterface
{
    public function backoffDelay(int $attempt, \Exception $error): float;
}
