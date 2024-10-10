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

    public function __construct(
        string $region = null,
        string $endpoint = null,
        string $signatureVersion = null,
        CredentialsProvider $credentialsProvider = null
    ) {
        $this->region = $region;
        $this->endpoint = $endpoint;
        $this->signatureVersion = $signatureVersion;
        $this->credentialsProvider = $credentialsProvider;
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
}
