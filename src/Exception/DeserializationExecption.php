<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * Represents an error is encountered during deserialization.
 */
class DeserializationExecption extends \Exception
{
    public function __construct(string $messge)
    {
        parent::__construct('Deserialization raised an exception: ' . $messge);
    }
}
