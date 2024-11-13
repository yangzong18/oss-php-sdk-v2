<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * The access key or access key secret associated with a credentials is not exist.
 */
class CredentialsEmptyError extends \Exception
{
    public function __construct()
    {
        parent::__construct('Credentials is null or empty');
    }
}
