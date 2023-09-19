<?php
namespace OSS\Credentials;
use OSS\Credentials\Credentials;

/**
 * Class AlibabaCloudCredentialsWrapper
 * @package OSS\Credentials
 */
class AlibabaCloudCredentialsWrapper implements CredentialsProvider{
    /**
     * @var Credentials
     */
    private $wrapper;
    public function __construct($wrapper){
        $this->wrapper = $wrapper;
    }
    public function getCredentials(){
        $ak = $this->wrapper->getAccessKeyId();
        $sk = $this->wrapper->getAccessKeySecret();
        $token = $this->wrapper->getSecurityToken();
        return new StaticCredentialsProvider($ak, $sk, $token);
    }
}