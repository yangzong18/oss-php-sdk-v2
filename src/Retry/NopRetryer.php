<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Retry;

class NopRetryer implements RetryerInterface
{
    public function isErrorRetryable(\Exception $error): bool
    {
        return false;
    }

    public function maxAttempts(): int 
    {
        return 1;
    }

    public function retryDelay(int $attempt, \Exception $error): float
    {
        throw new \Exception("NotImplemented");
    }
}
