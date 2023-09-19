<?php
namespace OSS\Exception;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
/**
 * Class OssException
 *
 * This is the class that OSSClient is expected to thrown, which the caller needs to handle properly.
 * It has the OSS specific errors which is useful for troubleshooting.
 *
 * @package OSS\Core
 */
class OssException extends \RuntimeException
{
    /**
     * @var Response Response
     */
    protected $response;

    /**
     * @var RequestInterface Request
     */
    protected $request;

    /**
     * @var string Exception type (client / server)
     */
    protected $details;

    function __construct($details)
    {
        if (is_array($details)) {
            $message = "Status Code:". $details['status'] . ", Code:". $details['code'] . ', Message: ' . $details['message']
                . ' RequestId: ' . $details['request-id'] . ', HostId: ' . $details['host-id'];
            if (isset($details['ec'])){
                $message .= ', Ec: ' . $details['ec'];
            }
            parent::__construct($message);
            $this->details = $details;
        } else {
            $message = $details;
            parent::__construct($message);
        }
    }

    public function getStatusCode()
    {
        return isset($this->details['status']) ? $this->details['status'] : '';
    }

    public function getRequestId()
    {
        return isset($this->details['request-id']) ? $this->details['request-id'] : '';
    }

    public function getErrorCode()
    {
        return isset($this->details['code']) ? $this->details['code'] : '';
    }

    public function getErrorMessage()
    {
        return isset($this->details['message']) ? $this->details['message'] : '';
    }

    public function getEc()
    {
        return isset($this->details['ec']) ? $this->details['ec'] : '';
    }

    public function getDetails()
    {
        return isset($this->details['body']) ? $this->details['body'] : '';
    }
}

