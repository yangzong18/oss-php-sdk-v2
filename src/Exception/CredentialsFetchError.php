<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * Fetch Credentials error.
 */
class CredentialsFetchError extends \Exception
{
    public function __construct(\Exception $error)
    {
        parent::__construct('Fetch Credentials raised an exception:' . $error);
    }
}
