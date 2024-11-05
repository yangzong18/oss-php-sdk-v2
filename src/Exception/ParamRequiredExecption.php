<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * Represents a param required error.
 */
class ParamRequiredExecption extends \Exception
{
    public function __construct(string $field)
    {
        parent::__construct('missing required field, ' . $field . '.');
    }
}
