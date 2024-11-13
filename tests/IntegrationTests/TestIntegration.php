<?php

namespace IntegrationTests;

use AlibabaCloud\Oss\V2 as Oss;

class TestIntegration extends \PHPUnit\Framework\TestCase
{
    protected $ACCESS_ID;
    protected $ACCESS_KEY;
    protected $ENDPOINT;
    protected $REGION;
    protected $RAM_ROLE_ARN;
    protected $USER_ID;

    protected $PAYER_ACCESS_ID;
    protected $PAYER_ACCESS_KEY;
    protected $PAYER_UID;

    public static $BUCKETNAME_PREFIX = "php-sdk-test-bucket-";
    public static $OBJECTNAME_PREFIX = "php-sdk-test-object-";

    private ?Oss\Client $defaultClient = null;
    private ?Oss\Client $invalidAkClient = null;
    private ?Oss\Client $signV1Client = null;

    public static function setUpBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function __construct(string $name) 
    {
        parent::__construct($name);
        $this->ACCESS_ID = getenv("OSS_TEST_ACCESS_KEY_ID");
        $this->ACCESS_KEY = getenv("OSS_TEST_ACCESS_KEY_SECRET");
        $this->ENDPOINT = getenv("OSS_TEST_ENDPOINT");
        $this->REGION = getenv("OSS_TEST_REGION")?? 'cn-hangzhou';
        $this->RAM_ROLE_ARN = getenv("OSS_TEST_RAM_ROLE_ARN");
        $this->USER_ID = getenv("OSS_TEST_USER_ID");

        $this->PAYER_ACCESS_ID = getenv("OSS_TEST_PAYER_ACCESS_KEY_ID");
        $this->PAYER_ACCESS_KEY = getenv("OSS_TEST_PAYER_ACCESS_KEY_SECRET");
        $this->PAYER_UID = getenv("OSS_TEST_PAYER_UID");
    }

    public function getDefaultClient()
    {
        if ($this->defaultClient != null) {
            return $this->defaultClient;
        }

        $cfg = Oss\Config::loadDefault();
        $cfg->setCredentialsProvider(new Oss\Credentials\StaticCredentialsProvider(
            $this->ACCESS_ID, $this->ACCESS_KEY
        ));
        $cfg->setRegion($this->REGION);
        $cfg->setEndpoint($this->ENDPOINT);
        $this->defaultClient = new Oss\Client($cfg);
        return $this->defaultClient;
    }

    public function getInvalidAkClient()
    {
        if ($this->invalidAkClient != null) {
            return $this->invalidAkClient;
        }

        $cfg = Oss\Config::loadDefault();
        $cfg->setCredentialsProvider(new Oss\Credentials\StaticCredentialsProvider(
            'invalid-ak', 'invalid'
        ));
        $cfg->setRegion($this->REGION);
        $cfg->setEndpoint($this->ENDPOINT);
        $this->invalidAkClient = new Oss\Client($cfg);
        return $this->invalidAkClient;
    }

    public static function randomBucketName() 
    {
        return self::$BUCKETNAME_PREFIX . strval(rand(0, 100)) . '-' .strval(time());
    }

    public static function randomLowStr() 
    {
        self::$BUCKETNAME_PREFIX . strval(rand(0, 100)) . '-' .strval(time());
    }    

    public static function randomStr() 
    {
        self::$BUCKETNAME_PREFIX . strval(rand(0, 100)) . '-' .strval(time());
    }    
}