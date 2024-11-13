<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use AlibabaCloud\Oss\V2\Credentials\CredentialsProvider;

final class Config
{
    /**
     * @var string The region in which the bucket is located.
     */
    private $region;

    /**
     * @var string The domain names that other services can use to access OSS.
     */
    private $endpoint;

    /**
     * @var string The signature version when signing requests. Valid values v4, v1
     */
    private $signatureVersion;

    /**
     * @var CredentialsProvider The credentials provider to use when signing requests.
     */
    private $credentialsProvider;

    /**
     * @var ?bool Forces the endpoint to be resolved as HTTP.
     */
    private $disableSSL;

    /**
     * @var ?bool Enable http redirect or not. Default is disable
     */
    private $enabledRedirect;

    /**
     * @var ?bool Skip server certificate verification.
     */
    private $insecureSkipVerify;

    /**
     * @var ?float The time in seconds till a timeout exception is thrown when attempting to make a connection. 
     *             The default is 10 seconds.
     */
    private $connectTimeout;

    /**
     * @var ?float The time in seconds till a timeout exception is thrown when attempting to read from a connection.
     *             The default is 20 seconds.
     */
    private $readwriteTimeout;

    /**
     * @var ?string The proxy setting.
     */
    private $proxyHost;

    public function __construct(
        string $region = null,
        string $endpoint = null,
        string $signatureVersion = null,
        CredentialsProvider $credentialsProvider = null,
        ?bool $disableSSL = null
    ) {
        $this->region = $region;
        $this->endpoint = $endpoint;
        $this->signatureVersion = $signatureVersion;
        $this->credentialsProvider = $credentialsProvider;
        $this->disableSSL = $disableSSL;
    }

    public static function loadDefault(): Config
    {
        return new Config();
    }

    /**
     * Get the region in which the bucket is located.
     *
     * @return  string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the region in which the bucket is located.
     *
     * @param  string  $region  The region in which the bucket is located.
     *
     * @return  self
     */
    public function setRegion(string $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get the domain names that other services can use to access OSS.
     *
     * @return  string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the domain names that other services can use to access OSS.
     *
     * @param  string  $endpoint  The domain names that other services can use to access OSS.
     *
     * @return  self
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the signature version when signing requests. Valid values v4, v1
     *
     * @return  string
     */
    public function getSignatureVersion()
    {
        return $this->signatureVersion;
    }

    /**
     * Set the signature version when signing requests. Valid values v4, v1
     *
     * @param  string  $signatureVersion  The signature version when signing requests. Valid values v4, v1
     *
     * @return  self
     */
    public function setSignatureVersion(string $signatureVersion)
    {
        $this->signatureVersion = $signatureVersion;

        return $this;
    }

    /**
     * Get the credentials provider to use when signing requests.
     *
     * @return  CredentialsProvider
     */
    public function getCredentialsProvider()
    {
        return $this->credentialsProvider;
    }

    /**
     * Set the credentials provider to use when signing requests.
     *
     * @param  CredentialsProvider  $credentialsProvider  The credentials provider to use when signing requests.
     *
     * @return  self
     */
    public function setCredentialsProvider(CredentialsProvider $credentialsProvider)
    {
        $this->credentialsProvider = $credentialsProvider;

        return $this;
    }

    /**
     * Get forces the endpoint to be resolved as HTTP.
     *
     * @return  ?bool
     */
    public function getDisableSSL()
    {
        return $this->disableSSL;
    }

    /**
     * Set forces the endpoint to be resolved as HTTP.
     *
     * @param  bool  $disableSSL  Forces the endpoint to be resolved as HTTP.
     *
     * @return  self
     */
    public function setDisableSSL(bool $disableSSL)
    {
        $this->disableSSL = $disableSSL;

        return $this;
    }

    /**
     * Get enable http redirect or not. Default is disable
     *
     * @return  ?bool
     */
    public function getEnabledRedirect()
    {
        return $this->enabledRedirect;
    }

    /**
     * Set enable http redirect or not. Default is disable
     *
     * @param  ?bool  $enabledRedirect  Enable http redirect or not. Default is disable
     *
     * @return  self
     */
    public function setEnabledRedirect(?bool $enabledRedirect)
    {
        $this->enabledRedirect = $enabledRedirect;

        return $this;
    }

    /**
     * Get skip server certificate verification.
     *
     * @return  ?bool
     */
    public function getInsecureSkipVerify()
    {
        return $this->insecureSkipVerify;
    }

    /**
     * Set skip server certificate verification.
     *
     * @param  ?bool  $insecureSkipVerify  Skip server certificate verification.
     *
     * @return  self
     */
    public function setInsecureSkipVerify(?bool $insecureSkipVerify)
    {
        $this->insecureSkipVerify = $insecureSkipVerify;

        return $this;
    }

    /**
     * Get the time in seconds till a timeout exception is thrown when attempting to make a connection.
     *
     * @return  ?float
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * Set the time in seconds till a timeout exception is thrown when attempting to make a connection.
     *
     * @param  ?float  $connectTimeout  The time in seconds till a timeout exception is thrown when attempting to make a connection.
     *
     * @return  self
     */
    public function setConnectTimeout(?float $connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;

        return $this;
    }

    /**
     * Get the time in seconds till a timeout exception is thrown when attempting to read from a connection.
     *
     * @return  ?float
     */
    public function getReadwriteTimeout()
    {
        return $this->readwriteTimeout;
    }

    /**
     * Set the time in seconds till a timeout exception is thrown when attempting to read from a connection.
     *
     * @param  ?float  $readwriteTimeout  The time in seconds till a timeout exception is thrown when attempting to read from a connection.
     *
     * @return  self
     */
    public function setReadwriteTimeout(?float $readwriteTimeout)
    {
        $this->readwriteTimeout = $readwriteTimeout;

        return $this;
    }

    /**
     * Get the proxy setting.
     *
     * @return  ?string
     */
    public function getProxyHost()
    {
        return $this->proxyHost;
    }

    /**
     * Set the proxy setting.
     *
     * @param  ?string  $proxyHost  The proxy setting.
     *
     * @return  self
     */
    public function setProxyHost(?string $proxyHost)
    {
        $this->proxyHost = $proxyHost;

        return $this;
    }
}
