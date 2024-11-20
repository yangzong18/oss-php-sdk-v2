<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * Represents a exception that is thrown when checking crc.
 */
class InconsistentExecption extends \RuntimeException
{
    public function __construct(string $crc1, string $crc2)
    {
        parent::__construct('crc is inconsistent, client ' . $crc1 . ', server'. $crc2);
    }
}
