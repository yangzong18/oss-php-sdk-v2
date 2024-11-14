<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Exception;

/**
 * Represents a service error.
 */
class ServiceError extends \Exception
{
    private $details;

    public function __construct($details)
    {
        if (\is_array($details)) {
            $this->details = $details;
        } else {
            $this->details = [];
            parent::__construct($details);
        }
    }

    public function __toString(): string
    {
        if (empty($this->details)) {
            return parent::__toString();
        }

        return "Error returned by Service.\n" .
            "Http Status Code: " . $this->getStatusCode() . "\n" .
            "Error Code: " . $this->getErrorCode() . "\n" .
            "Request Id: " . $this->getRequestId() . "\n" .
            "Message: " . $this->getErrorMessage() . "\n" .
            "EC: " . $this->getEC() . "\n" .
            "Timestamp: " . $this->getHeader('Date') . "\n" .
            "Request Endpoint: " . $this->getRequestTarget();
    }

    public function getStatusCode()
    {
        return $this->details['status_code'] ?? 0;
    }

    public function getRequestId()
    {
        return $this->details['request_id'] ?? '';
    }

    public function getErrorCode()
    {
        return $this->details['code'] ?? '';
    }

    public function getErrorMessage()
    {
        return $this->details['message'] ?? '';
    }

    public function getEC()
    {
        return $this->details['ec'] ?? '';
    }

    public function getTimestamp(): ?\DateTime
    {
        $date = $this->getHeader('Date');
        $timestamp = \DateTime::createFromFormat('D, d M Y H:i:s \G\M\T',  $date, new \DateTimeZone('UTC'));
        if ($timestamp === false) {
            return null;
        }
        return $timestamp;
    }

    public function getRequestTarget(): string
    {
        return $this->details['request_target'] ?? '';
    }

    public function getSnapshot(): string
    {
        return $this->details['snapshot'] ?? '';
    }

    public function getHeaders(): array
    {
        return $this->details['headers'] ?? [];
    }

    public function getHeader(string $key): string
    {
        if (isset($this->details['headers'])) {
            $v = $this->details['headers'][$key];
            if (\is_array($v)) {
                return $v[0];
            }
            return $v;
        }
        return '';
    }

    public function getErrorFileds()
    {
        return $this->details['error_fileds'] ?? [];
    }

    public function getErrorFiled(string $filed): string
    {
        if (isset($this->details['error_fileds'])) {
            return $this->details['error_fileds'][$filed] ?? '';
        }
        return '';
    }
}
