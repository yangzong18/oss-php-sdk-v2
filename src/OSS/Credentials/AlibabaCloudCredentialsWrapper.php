<?php
namespace OSS\Credentials;

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

    /**
     * @return Credentials
     */
    public function getCredentials(){
        return  $this->wrapper;
    }
}