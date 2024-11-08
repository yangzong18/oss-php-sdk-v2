<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Types;

use AlibabaCloud\Oss\V2\Types\Model;

class ResultModel extends Model
{
    protected string $status = '';

    protected int $statusCode = 0;

    protected string $requestId = '';

    protected array $headers = [];

    public function __construct(array $options = []) {
        if (\is_array($options) && !empty($options)) {
            if (isset($options['status'])) {
                $this->status = $options['status'];
            }
            if (isset($options['statusCode'])) {
                $this->statusCode = $options['statusCode'];
            }
            if (isset($options['requestId'])) {
                $this->requestId = $options['requestId'];
            }
            if (isset($options['headers'])) {
                $this->headers = $options['headers'];
            }
        }
    }
}
