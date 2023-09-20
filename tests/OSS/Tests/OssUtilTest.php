<?php

namespace OSS\Tests;

use OSS\Exception\OssException;
use OSS\Utils\OssUtil;

class OssUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testIsIpFormat()
    {
        $this->assertTrue(OssUtil::isIPFormat("10.101.160.147"));
        $this->assertTrue(OssUtil::isIPFormat("12.12.12.34"));
        $this->assertTrue(OssUtil::isIPFormat("12.12.12.12"));
        $this->assertTrue(OssUtil::isIPFormat("255.255.255.255"));
        $this->assertTrue(OssUtil::isIPFormat("0.1.1.1"));
        $this->assertFalse(OssUtil::isIPFormat("0.1.1.x"));
        $this->assertFalse(OssUtil::isIPFormat("0.1.1.256"));
        $this->assertFalse(OssUtil::isIPFormat("256.1.1.1"));
        $this->assertFalse(OssUtil::isIPFormat("0.1.1.0.1"));
        $this->assertTrue(OssUtil::isIPFormat("10.10.10.10:123"));
    }

    public function testValidateBucket()
    {
        $this->assertTrue(OssUtil::validateBucket("xxx"));
        $this->assertFalse(OssUtil::validateBucket("XXXqwe123"));
        $this->assertFalse(OssUtil::validateBucket("XX"));
        $this->assertFalse(OssUtil::validateBucket("/X"));
        $this->assertFalse(OssUtil::validateBucket(""));
    }

    public function testValidateObject()
    {
        $this->assertTrue(OssUtil::validateObject("xxx"));
        $this->assertTrue(OssUtil::validateObject("xxx23"));
        $this->assertTrue(OssUtil::validateObject("12321-xxx"));
        $this->assertTrue(OssUtil::validateObject("x"));
        $this->assertFalse(OssUtil::validateObject("/aa"));
        $this->assertFalse(OssUtil::validateObject("\\aa"));
        $this->assertFalse(OssUtil::validateObject(""));
    }

    public function testStartWith()
    {
        $this->assertTrue(OssUtil::startsWith("xxab", "xx"), true);
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

	public function testGetHostPortFromEndpoint()
    {
        $str =  OssUtil::getHostPortFromEndpoint('http://username:password@hostname:80/path?arg=value#anchor');
        $this->assertEquals('hostname:80', $str);

        $str =  OssUtil::getHostPortFromEndpoint('hostname:80');
        $this->assertEquals('hostname:80', $str);

        $str =  OssUtil::getHostPortFromEndpoint('www.hostname.com');
        $this->assertEquals('www.hostname.com', $str);

        $str =  OssUtil::getHostPortFromEndpoint('http://www.hostname.com');
        $this->assertEquals('www.hostname.com', $str);

        $str =  OssUtil::getHostPortFromEndpoint('https://www.hostname.com');
        $this->assertEquals('www.hostname.com', $str);

        $str =  OssUtil::getHostPortFromEndpoint('192.168.1.10:8080');
        $this->assertEquals('192.168.1.10:8080', $str);

        $str =  OssUtil::getHostPortFromEndpoint('file://username:password@hostname:80/path?arg=value#anchor');
        $this->assertEquals('hostname:80', $str);

        $str =  OssUtil::getHostPortFromEndpoint('https://WWW.hostname.com-_www.test.com');
        $this->assertEquals('WWW.hostname.com-_www.test.com', $str);

        try {
            $str =  OssUtil::getHostPortFromEndpoint('http:///path?arg=value#anchor');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            $str =  OssUtil::getHostPortFromEndpoint('https://www.hostname.com\www.test.com');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            $str =  OssUtil::getHostPortFromEndpoint('www.hostname.com-_*www.test.com');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            $str =  OssUtil::getHostPortFromEndpoint('www.hostname.com:ab123');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            $str =  OssUtil::getHostPortFromEndpoint('www.hostname.com:');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }

    public function testArrayToXml(){
        $createBucketXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<CreateBucketConfiguration>
<StorageClass>Standard</StorageClass>
<DataRedundancyType>LRS</DataRedundancyType>
</CreateBucketConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($createBucketXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $createBucket = [
            'CreateBucketConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($createBucket);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($createBucketXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $createAccessXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<CreateAccessPointConfiguration>
<AccessPointName>ap-01</AccessPointName>
<NetworkOrigin>vpc</NetworkOrigin>
<VpcConfiguration>
<VpcId>vpc-t4nlw426y44rd3iq4****</VpcId>
</VpcConfiguration>
</CreateAccessPointConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($createAccessXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $createAccess = [
            'CreateAccessPointConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($createAccess);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($createAccessXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $initiateWormXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<InitiateWormConfiguration>
<RetentionPeriodInDays>365</RetentionPeriodInDays>
</InitiateWormConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($initiateWormXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $initiateWorm = [
            'InitiateWormConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($initiateWorm);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($initiateWormXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $extendWormXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<ExtendWormConfiguration>
<RetentionPeriodInDays>365</RetentionPeriodInDays>
</ExtendWormConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($extendWormXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $extendWorm = [
            'ExtendWormConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($extendWorm);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($extendWormXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>RuleID</ID>
<Prefix>Prefix</Prefix>
<Status>Status</Status>
<Expiration>
<Days>Days</Days>
</Expiration>
<Transition>
<Days>Days</Days>
<StorageClass>StorageClass</StorageClass>
</Transition>
<AbortMultipartUpload>
<Days>Days</Days>
</AbortMultipartUpload>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix>log/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>30</Days>
<StorageClass>IA</StorageClass>
</Transition>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix>log/</Prefix>
<Status>Enabled</Status>
<Expiration>
<Days>90</Days>
</Expiration>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix>log/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>30</Days>
<StorageClass>IA</StorageClass>
</Transition>
<Transition>
<Days>60</Days>
<StorageClass>Archive</StorageClass>
</Transition>
<Expiration>
<Days>3600</Days>
</Expiration>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix/>
<Status>Enabled</Status>
<Expiration>
<ExpiredObjectDeleteMarker>true</ExpiredObjectDeleteMarker>
</Expiration>
<NoncurrentVersionExpiration>
<NoncurrentDays>5</NoncurrentDays>
</NoncurrentVersionExpiration>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix/>
<Status>Enabled</Status>
<Filter>
<Not>
<Prefix>log</Prefix>
<Tag><Key>key1</Key><Value>value1</Value></Tag>
</Not>
</Filter>
<Transition>
<Days>30</Days>
<StorageClass>Archive</StorageClass>
</Transition>
<Expiration>
<Days>100</Days>
</Expiration>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix>log/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>30</Days>
<StorageClass>IA</StorageClass>
<IsAccessTime>true</IsAccessTime>
<ReturnToStdWhenVisit>true</ReturnToStdWhenVisit>
</Transition>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule</ID>
<Prefix>/</Prefix>
<Status>Enabled</Status>
<AbortMultipartUpload>
<Days>30</Days>
</AbortMultipartUpload>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $lifecycleXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<LifecycleConfiguration>
<Rule>
<ID>Rule1</ID>
<Prefix>dir1/</Prefix>
<Status>Status</Status>
<Expiration>
<Days>180</Days>
</Expiration>
</Rule>
<Rule>
<ID>Rule2</ID>
<Prefix>dir1/dir2/</Prefix>
<Status>Status</Status>
<Expiration>
<Days>30</Days>
</Expiration>
</Rule>
</LifecycleConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($lifecycleXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $lifecycle = [
            'LifecycleConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($lifecycle);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($lifecycleXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $transferAccelerationXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<TransferAccelerationConfiguration>
<Enabled>true</Enabled>
</TransferAccelerationConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($transferAccelerationXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $transferAcceleration = [
            'TransferAccelerationConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($transferAcceleration);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($transferAccelerationXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $replicationXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<ReplicationConfiguration>
<Rule>
<RTC>
<Status>enabled or disabled</Status>
</RTC>
<PrefixSet>
<Prefix>prefix_1</Prefix>
<Prefix>prefix_2</Prefix>
</PrefixSet>
<Action>ALL,PUT</Action>
<Destination>
<Bucket>destbucket</Bucket>
<Location>oss-cn-hangzhou</Location>
<TransferType>oss_acc</TransferType>
</Destination>
<HistoricalObjectReplication>enabled or disabled</HistoricalObjectReplication>
</Rule>
</ReplicationConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($replicationXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $replication = [
            'ReplicationConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($replication);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($replicationXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $rtcXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<ReplicationRule>
<RTC>
<Status>enabled or disabled</Status>
</RTC>
<ID>rule id</ID>
</ReplicationRule>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($rtcXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $rtc = [
            'ReplicationRule' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($rtc);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($rtcXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $inventoryXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<InventoryConfiguration>
<Id>report1</Id>
<IsEnabled>true</IsEnabled>
<Filter>
<Prefix>Pics/</Prefix>
<LastModifyBeginTimeStamp>1637883649</LastModifyBeginTimeStamp>
<LastModifyEndTimeStamp>1638347592</LastModifyEndTimeStamp>
<LowerSizeBound>1024</LowerSizeBound>
<UpperSizeBound>1048576</UpperSizeBound>
<StorageClass>Standard,IA</StorageClass>
</Filter>
<Destination>
<OSSBucketDestination>
<Format>CSV</Format>
<AccountId>100000000000000</AccountId>
<RoleArn>acs:ram::100000000000000:role/AliyunOSSRole</RoleArn>
<Bucket>acs:oss:::destbucket</Bucket>
<Prefix>prefix1/</Prefix>
<Encryption>
<SSE-KMS>
<KeyId>keyId</KeyId>
</SSE-KMS>
</Encryption>
</OSSBucketDestination>
</Destination>
<Schedule>
<Frequency>Daily</Frequency>
</Schedule>
<IncludedObjectVersions>All</IncludedObjectVersions>
<OptionalFields>
<Field>Size</Field>
<Field>LastModifiedDate</Field>
<Field>ETag</Field>
<Field>StorageClass</Field>
<Field>IsMultipartUploaded</Field>
<Field>EncryptionStatus</Field>
</OptionalFields>
</InventoryConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($inventoryXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $inventory = [
            'InventoryConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($inventory);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($inventoryXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $logXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<BucketLoggingStatus>
<LoggingEnabled>
<TargetBucket>TargetBucket</TargetBucket>
<TargetPrefix>TargetPrefix</TargetPrefix>
</LoggingEnabled>
</BucketLoggingStatus>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($logXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $log = [
            'BucketLoggingStatus' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($log);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($logXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $webSiteXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<WebsiteConfiguration>
<IndexDocument>
<Suffix>index.html</Suffix>
</IndexDocument>
<ErrorDocument>
<Key>errorDocument.html</Key>
<HttpStatus>404</HttpStatus>
</ErrorDocument>
</WebsiteConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($webSiteXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $webSite = [
            'WebsiteConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($webSite);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($webSiteXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $refererXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<RefererConfiguration>
<AllowEmptyReferer>false</AllowEmptyReferer>
<AllowTruncateQueryString>true</AllowTruncateQueryString>
<TruncatePath>true</TruncatePath>
<RefererList>
<Referer>http://www.aliyun.com</Referer>
<Referer>https://www.aliyun.com</Referer>
<Referer>http://www.*.com</Referer>
<Referer>https://www.?.aliyuncs.com</Referer>
</RefererList>
<RefererBlacklist>
<Referer>http://www.refuse.com</Referer>
<Referer>https://*.hack.com</Referer>
<Referer>http://ban.*.com</Referer>
<Referer>https://www.?.deny.com</Referer>
</RefererBlacklist>
</RefererConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($refererXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $referer = [
            'RefererConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($referer);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($refererXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $dosInfoXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<AntiDDOSConfiguration>
<Cnames>
<Domain>abc1.example.cn</Domain>
<Domain>abc2.example.cn</Domain>
</Cnames>
</AntiDDOSConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($dosInfoXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $dosInfo = [
            'AntiDDOSConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($dosInfo);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($dosInfoXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $doMetaQueryXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<MetaQuery>
<NextToken/>
<MaxResults>5</MaxResults>
<Query>{"Field": "Size","Value": "1048576","Operation": "gt"}</Query>
<Sort>Size</Sort>
<Order>asc</Order>
<Aggregations>
<Aggregation>
<Field>Size</Field>
<Operation>sum</Operation>
</Aggregation>
<Aggregation>
<Field>Size</Field>
<Operation>max</Operation>
</Aggregation>
</Aggregations>
</MetaQuery>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($doMetaQueryXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $doMetaQuery = [
            'MetaQuery' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($doMetaQuery);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($doMetaQueryXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $corsXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration>
<CORSRule>
<AllowedOrigin>*</AllowedOrigin>
<AllowedMethod>PUT</AllowedMethod>
<AllowedMethod>GET</AllowedMethod>
<AllowedHeader>Authorization</AllowedHeader>
</CORSRule>
<CORSRule>
<AllowedOrigin>http://example.com</AllowedOrigin>
<AllowedOrigin>http://example.net</AllowedOrigin>
<AllowedMethod>GET</AllowedMethod>
<AllowedHeader> Authorization</AllowedHeader>
<ExposeHeader>x-oss-test</ExposeHeader>
<ExposeHeader>x-oss-test1</ExposeHeader>
<MaxAgeSeconds>100</MaxAgeSeconds>
</CORSRule>
<ResponseVary>false</ResponseVary>
</CORSConfiguration>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($corsXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $cors = [
            'CORSConfiguration' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($cors);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($corsXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        $tagXml = <<<AAA
<?xml version="1.0" encoding="UTF-8"?>
<Tagging>
<TagSet>
<Tag>
<Key>key1</Key>
<Value>value1</Value>
</Tag>
<Tag>
<Key>key2</Key>
<Value>value2</Value>
</Tag>
</TagSet>
</Tagging>
AAA;
        $tmpArray =  json_decode(json_encode(simplexml_load_string($tagXml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $tag = [
            'Tagging' => $tmpArray
        ];
        try {
            $xml =  OssUtil::arrayToXml($tag);
            $this->assertEquals($this->cleanXml($xml),$this->cleanXml($tagXml));
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }


}
