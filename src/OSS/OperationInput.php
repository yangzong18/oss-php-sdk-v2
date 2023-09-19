<?php
namespace OSS;

class OperationInput {

    /**
     * @var string|null
     */
    private $operationName;

    /**
     * @var string|null
     */
    private $bucket;
    /**
     * @var string|null
     */
    private $key;
    /**
     * @var string|null
     */
    private $method;
    /**
     * @var array|null
     */
    private $headers;
    /**
     * @var array|null
     */
    private $parameters;
    /**
     * @var mixed|null
     */
    private $body;
    /**
     * @var array|null
     */
    private $metadata;

    public function __construct($operationName = null, $bucket = null, $key = null, $method = null, $headers = null, $parameters = null, $body = null, $metadata = null) {
        $this->operationName = $operationName;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->method = $method;
        $this->headers = $headers;
        $this->parameters = $parameters;
        $this->body = $body;
        $this->metadata = $metadata;
    }

    // Getter and setter methods for each property

    public function getOperationName() {
        return $this->operationName;
    }

    public function setOperationName($operationName) {
        $this->operationName = $operationName;
    }

    public function getBucket() {
        return $this->bucket;
    }

    public function setBucket($bucket) {
        $this->bucket = $bucket;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function setMetadata($metadata) {
        $this->metadata = $metadata;
    }

    public function toArray() {
        return get_object_vars($this);
    }
}
