<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Signer;

use AlibabaCloud\Oss\V2\Credentials\Credentials;
use Psr\Http\Message\RequestInterface;

class SigningContext
{

    /**
     * @var string|null
     */
    private $product;
    /**
     * @var string|null
     */
    private $region;
    /**
     * @var string|null
     */
    private $bucket;
    /**
     * @var string|null
     */
    private $key;
    /**
     * @var RequestInterface|null
     */
    private $request;
    /**
     * @var string|null
     */
    private $subResource;
    /**
     * @var Credentials|null
     */
    private $credentials;
    /**
     * @var string|null
     */
    private $time;
    /**
     * @var string|null
     */
    private $signedHeaders;
    /**
     * @var string|null
     */
    private $stringToSign;

    public function __construct($product = null, $region = null, $bucket = null, $key = null, $request = null, $subResource = null, $credentials = null, $time = null, $signedHeaders = null, $stringToSign = null) {
        $this->product = $product;
        $this->region = $region;
        $this->bucket = $bucket;
        $this->key = $key;
        $this->request = $request;
        $this->subResource = $subResource;
        $this->credentials = $credentials;
        $this->time = $time;
        $this->signedHeaders = $signedHeaders;
        $this->stringToSign = $stringToSign;
    }

    public function getProduct() {
        return $this->product;
    }

    public function setProduct($product) {
        $this->product = $product;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($region) {
        $this->region = $region;
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

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

    public function getSubResource() {
        return $this->subResource;
    }

    public function setSubResource($subResource) {
        $this->subResource = $subResource;
    }

    public function getCredentials() {
        return $this->credentials;
    }

    public function setCredentials($credentials) {
        $this->credentials = $credentials;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getSignedHeaders() {
        return $this->signedHeaders;
    }

    public function setSignedHeaders($signedHeaders) {
        $this->signedHeaders = $signedHeaders;
    }

    public function getStringToSign() {
        return $this->stringToSign;
    }

    public function setStringToSign($stringToSign) {
        $this->stringToSign = $stringToSign;
    }
}

