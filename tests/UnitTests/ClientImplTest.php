<?php

namespace UnitTests;

use AlibabaCloud\Oss\V2\Config;
use AlibabaCloud\Oss\V2\OperationInput;
use AlibabaCloud\Oss\V2\Signer;
use AlibabaCloud\Oss\V2\Retry;
use AlibabaCloud\Oss\V2\Credentials;
use AlibabaCloud\Oss\V2\Utils;
use AlibabaCloud\Oss\V2\ClientImpl;
use AlibabaCloud\Oss\V2\Defaults;
use AlibabaCloud\Oss\V2\Exception;
use GuzzleHttp;

class ClientImplTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultConfig()
    {
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);

        $ro = new \ReflectionObject($client);

        $pSdkOptions = $ro->getProperty('sdkOptions');
        $pInnerOptions = $ro->getProperty(name: 'innerOptions');
        $pRequestOptions = $ro->getProperty('requestOptions');

        $sdkOptions = $pSdkOptions->getValue($client);
        $innerOptions = $pInnerOptions->getValue($client);
        $requestOptions = $pRequestOptions->getValue($client);

        //default
        $this->assertEquals('oss', $sdkOptions['product']);
        $this->assertEquals('cn-hangzhou', $sdkOptions['region']);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('https', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('oss-cn-hangzhou.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        $this->assertEquals(null, $sdkOptions['retry_max_attempts']);
        $this->assertInstanceOf(Retry\StandardRetryer::class, $sdkOptions['retryer']);

        $this->assertInstanceOf(Signer\SignerV4::class, $sdkOptions['signer']);
        $this->assertInstanceOf(Credentials\AnonymousCredentialsProvider::class, $sdkOptions['credentials_provider']);

        $this->assertEquals('virtual', $sdkOptions['address_style']);
        $this->assertEquals('header', $sdkOptions['auth_method']);
        $this->assertEquals(null, $sdkOptions['response_handlers']);
        $this->assertEquals(0, $sdkOptions['feature_flags']);
        $this->assertEquals(null, $sdkOptions['additional_headers']);

        #$this->assertEquals('oss', $sdkOptions['response_stream']);

        $this->assertEquals(null, $innerOptions['handler']);
        $this->assertStringContainsString('alibabacloud-php-sdk-v2/1.', $innerOptions['user_agent']);

        $this->assertEquals(False, $requestOptions['allow_redirects']);
        $this->assertEquals(10.0, $requestOptions['connect_timeout']);
        $this->assertEquals(20.0, $requestOptions['read_timeout']);
        $this->assertEquals(True, $requestOptions['verify']);
    }

    public function testConfigCredentialsProvider()
    {
        // default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(null, $sdkOptions['credentials_provider']);

        // set AnonymousCredentialsProvider
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Credentials\AnonymousCredentialsProvider::class, $sdkOptions['credentials_provider']);

        // set static
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\StaticCredentialsProvider('ak', 'sk'));

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Credentials\StaticCredentialsProvider::class, $sdkOptions['credentials_provider']);
        $cred = $sdkOptions['credentials_provider']->getCredentials();

        $this->assertEquals('ak', $cred->getAccessKeyId());
        $this->assertEquals('sk', $cred->getAccessKeySecret());
    }

    public function testConfigSignatureVersion()
    {
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        // default 
        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Signer\SignerV4::class, $sdkOptions['signer']);

        // set to v1
        $cfg->setSignatureVersion('v1');
        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Signer\SignerV1::class, $sdkOptions['signer']);


        // set invalid 
        $cfg->setSignatureVersion('any-str');
        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Signer\SignerV4::class, $sdkOptions['signer']);

        // set v4 
        $cfg->setSignatureVersion('v4');
        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);

        $this->assertInstanceOf(Signer\SignerV4::class, $sdkOptions['signer']);
    }

    public function testConfigEndpoint()
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('https', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('oss-cn-beijing.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        // use_internal_endpoint
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-shanghai');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUseInternalEndpoint(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('https', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('oss-cn-shanghai-internal.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        // use_accelerate_endpoint
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUseAccelerateEndpoint(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('https', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('oss-accelerate.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        // use_dualstack_endpoint
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUseDualStackEndpoint(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('https', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('cn-hangzhou.oss.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        // user-defined endpoint
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setEndpoint('http://oss-cn-shenzhen.aliyuncs.com');

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('http', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('oss-cn-shenzhen.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());

        // disable ssl
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUseDualStackEndpoint(true);
        $cfg->setDisableSSL(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertInstanceOf(GuzzleHttp\Psr7\Uri::class, $sdkOptions['endpoint']);
        $this->assertEquals('http', $sdkOptions['endpoint']->getScheme());
        $this->assertEquals('cn-hangzhou.oss.aliyuncs.com', $sdkOptions['endpoint']->getAuthority());
    }

    public function testConfigAddressStyle()
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('virtual', $sdkOptions['address_style']);

        //set path-style 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUsePathStyle(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('path', $sdkOptions['address_style']);

        //set cname 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUseCname(true);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('cname', $sdkOptions['address_style']);
    }

    public function testConfigRetryer()
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(null, $sdkOptions['retry_max_attempts']);
        $this->assertInstanceOf(Retry\StandardRetryer::class, $sdkOptions['retryer']);

        //set retry_max_attempts
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setRetryMaxAttempts(2);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(2, $sdkOptions['retry_max_attempts']);
        $this->assertInstanceOf(Retry\StandardRetryer::class, $sdkOptions['retryer']);

        //set retryer
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setRetryer(new Retry\NopRetryer());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(null, $sdkOptions['retry_max_attempts']);
        $this->assertInstanceOf(Retry\NopRetryer::class, $sdkOptions['retryer']);

        //set retry_max_attempts and retryer
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setRetryMaxAttempts(2);
        $cfg->setRetryer(new Retry\StandardRetryer(maxAttempts: 4));

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(2, $sdkOptions['retry_max_attempts']);
        $this->assertInstanceOf(Retry\StandardRetryer::class, $sdkOptions['retryer']);
        $this->assertEquals(4, $sdkOptions['retryer']->getMaxAttempts());
    }

    public function testConfigUserAgent()
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pInnerOptions = $ro->getProperty('innerOptions');
        $innerOptions = $pInnerOptions->getValue($client);
        $this->assertEquals(Utils::defaultUserAgent(), $innerOptions['user_agent']);

        //set user-agent 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUserAgent('my-agent');

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pInnerOptions = $ro->getProperty('innerOptions');
        $innerOptions = $pInnerOptions->getValue($client);
        $this->assertEquals(Utils::defaultUserAgent() . '/my-agent', $innerOptions['user_agent']);

        //set repalce by options 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUserAgent('my-agent');

        $client = new ClientImpl($cfg, ['user_agent' => 'user-define-agent']);
        $ro = new \ReflectionObject($client);
        $pInnerOptions = $ro->getProperty('innerOptions');
        $innerOptions = $pInnerOptions->getValue($client);
        $this->assertEquals('user-define-agent', $innerOptions['user_agent']);
    }

    public function testConfigAdditionalHeaders()
    {
        //default
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(null, $sdkOptions['additional_headers']);

        //set from config
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setAdditionalHeaders(['header1', 'header2']);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals(['header1', 'header2'], $sdkOptions['additional_headers']);
    }


    public function testConfigTimeout()
    {
        //default
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals(False, $requestOptions['allow_redirects']);
        $this->assertEquals(10.0, $requestOptions['connect_timeout']);
        $this->assertEquals(20.0, $requestOptions['read_timeout']);
        $this->assertEquals(True, $requestOptions['verify']);

        // set from config
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setInsecureSkipVerify(true);
        $cfg->setEnabledRedirect(true);
        $cfg->setConnectTimeout(30.0);
        $cfg->setReadwriteTimeout(40.5);

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals(true, $requestOptions['allow_redirects']);
        $this->assertEquals(30.0, $requestOptions['connect_timeout']);
        $this->assertEquals(40.5, $requestOptions['read_timeout']);
        $this->assertEquals(false, $requestOptions['verify']);

        // set from options
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl(
            $cfg,
            [
                'request_options' => [
                    'allow_redirects' => true,
                    'connect_timeout' => 11.0,
                    'read_timeout' => 22.0,
                    'verify' => false,
                ],
            ]
        );
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        #$this->assertEquals(true, $requestOptions['allow_redirects']);
        $this->assertEquals(11.0, $requestOptions['connect_timeout']);
        $this->assertEquals(22.0, $requestOptions['read_timeout']);
        $this->assertEquals(false, $requestOptions['verify']);

        // set from config
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setInsecureSkipVerify(true);
        $cfg->setEnabledRedirect(true);
        $cfg->setConnectTimeout(30.0);
        $cfg->setReadwriteTimeout(40.5);

        $client = new ClientImpl(
            $cfg,
            [
                'request_options' => [
                    'allow_redirects' => false,
                    'connect_timeout' => 12.0,
                    'read_timeout' => 23.0,
                    'verify' => true,
                ],
            ]
        );
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals(false, $requestOptions['allow_redirects']);
        $this->assertEquals(12.0, $requestOptions['connect_timeout']);
        $this->assertEquals(23.0, $requestOptions['read_timeout']);
        $this->assertEquals(true, $requestOptions['verify']);
    }

    public function testConfigProxy()
    {
        //default
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals(null, $requestOptions['proxy']);

        // set from config
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setProxyHost('http://127.0.0.1:8080');

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals('http://127.0.0.1:8080', $requestOptions['proxy']);

        // set from options
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-hangzhou');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl(
            $cfg,
            [
                'request_options' => [
                    'proxy' => 'http://127.0.0.1:3182',
                ],
            ]
        );
        $ro = new \ReflectionObject($client);
        $pRequestOptions = $ro->getProperty('requestOptions');
        $requestOptions = $pRequestOptions->getValue($client);
        $this->assertEquals('http://127.0.0.1:3182', $requestOptions['proxy']);
    }

    public function testConfigAuthMethod()
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('header', $sdkOptions['auth_method']);

        //set query 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg, ['auth_method' => 'query']);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('query', $sdkOptions['auth_method']);
    }

    public function testConfigProduct(): void
    {
        //default 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('oss', $sdkOptions['product']);

        //set oss-cloudbox 
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        $client = new ClientImpl($cfg, ['product' => 'oss-cloudbox']);
        $ro = new \ReflectionObject($client);
        $pSdkOptions = $ro->getProperty('sdkOptions');
        $sdkOptions = $pSdkOptions->getValue($client);
        $this->assertEquals('oss-cloudbox', $sdkOptions['product']);
    }

    public function testInvokeOperationOptions(): void
    {
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        //default
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
        );
        $client->executeAsync($input)->wait();
        $options = $mock->getLastOptions();
        #GuzzleHttp's request options
        $this->assertEquals(false, $options['allow_redirects']);
        $this->assertEquals(Defaults::CONNECT_TIMEOUT, $options['connect_timeout']);
        $this->assertEquals(Defaults::READWRITE_TIMEOUT, $options['read_timeout']);
        $this->assertEquals(true, $options['verify']);
        #sdk's request options
        $this->assertEquals(Defaults::MAX_ATTEMPTS, $options['sdk_context']['retry_max_attempts']);
        $this->assertInstanceOf(retry\StandardRetryer::class, $options['sdk_context']['retryer']);

        // test retry_max_attempts & retryer
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $opt = [
            'retry_max_attempts' => 4,
            'retryer' => new Retry\NopRetryer(),
        ];
        $client->executeAsync($input, $opt)->wait();
        $options = $mock->getLastOptions();
        // GuzzleHttp's request options
        $this->assertEquals(false, $options['allow_redirects']);
        $this->assertEquals(Defaults::CONNECT_TIMEOUT, $options['connect_timeout']);
        $this->assertEquals(Defaults::READWRITE_TIMEOUT, $options['read_timeout']);
        $this->assertEquals(true, $options['verify']);
        // sdk's request options
        $this->assertEquals(4, $options['sdk_context']['retry_max_attempts']);
        $this->assertInstanceOf(retry\NopRetryer::class, $options['sdk_context']['retryer']);

        // test retryer
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $opt = [
            'retryer' => new Retry\StandardRetryer(10),
        ];
        $client->executeAsync($input, $opt)->wait();
        $options = $mock->getLastOptions();
        $this->assertEquals(10, $options['sdk_context']['retry_max_attempts']);
        $this->assertInstanceOf(retry\StandardRetryer::class, $options['sdk_context']['retryer']);

        // test request options
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $opt = [
            'allow_redirects' => true,
            'connect_timeout' => 31.0,
            'read_timeout' => 15.2,
            'verify' => false,
        ];
        $client->executeAsync($input, $opt)->wait();
        $options = $mock->getLastOptions();
        // GuzzleHttp's request options
        $this->assertEquals(true, $options['allow_redirects']);
        $this->assertEquals(31.0, $options['connect_timeout']);
        $this->assertEquals(15.2, $options['read_timeout']);
        $this->assertEquals(false, $options['verify']);
    }

    public function testRequetContext(): void
    {
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());

        // user-agent
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();

        $this->assertEquals(Utils::defaultUserAgent(), $request->getHeader('User-Agent')[0]);

        // uri format
        # virtual, no bucket and key, only bucket,  bucket and key
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://oss-cn-beijing.aliyuncs.com/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://my-bucket.oss-cn-beijing.aliyuncs.com/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket',
            key: '123/321/+? /123.txt'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://my-bucket.oss-cn-beijing.aliyuncs.com/123/321/%2B%3F%20/123.txt', $request->getUri()->__tostring());

        # cname, no bucket and key, only bucket,  bucket and key
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setEndpoint('www.cname.com');
        $cfg->setUseCname(true);
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://www.cname.com/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://www.cname.com/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket',
            key: '123/321/+? /123.txt'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://www.cname.com/123/321/%2B%3F%20/123.txt', $request->getUri()->__tostring());

        # path-style, no bucket and key, only bucket,  bucket and key
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\AnonymousCredentialsProvider());
        $cfg->setUsePathStyle(true);
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://oss-cn-beijing.aliyuncs.com/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://oss-cn-beijing.aliyuncs.com/my-bucket/', $request->getUri()->__tostring());

        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket',
            key: '123/321/+? /123.txt'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://oss-cn-beijing.aliyuncs.com/my-bucket/123/321/%2B%3F%20/123.txt', $request->getUri()->__tostring());

        // uri format after signing
        # signing header, bucket and key
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\StaticCredentialsProvider('ak', 'sk'));
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock]);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket',
            key: '123/321/+? /123.txt'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertEquals('https://my-bucket.oss-cn-beijing.aliyuncs.com/123/321/%2B%3F%20/123.txt', $request->getUri()->__tostring());

        # signing query, bucket and key
        $cfg = Config::loadDefault();
        $cfg->setRegion('cn-beijing');
        $cfg->setCredentialsProvider(new Credentials\StaticCredentialsProvider('ak', 'sk'));
        $mock = new GuzzleHttp\Handler\MockHandler([new GuzzleHttp\Psr7\Response()]);
        $client = new ClientImpl($cfg, ['handler' => $mock, 'auth_method' => 'query']);
        $input = new OperationInput(
            opName: "TestApi",
            method: "PUT",
            bucket: 'my-bucket',
            key: '123/321/+? /123.txt'
        );
        $client->executeAsync($input)->wait();
        $request = $mock->getLastRequest();
        $this->assertStringContainsString('https://my-bucket.oss-cn-beijing.aliyuncs.com/123/321/%2B%3F%20/123.txt?x-oss-credential=ak', $request->getUri()->__tostring());
    }

}
