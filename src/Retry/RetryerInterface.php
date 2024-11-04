<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Retry;


interface RetryerInterface
{
    public function isErrorRetryable(\Exception $error): bool;

    public function maxAttempts(): int;

    public function retryDelay(int $attempt, \Exception $error): float;
}
