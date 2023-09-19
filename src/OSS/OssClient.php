<?php

namespace OSS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use OSS\Utils\MimeTypes;
use OSS\Utils\OssUtil;
use OSS\Credentials\CredentialsProvider;
use OSS\Exception\OssException;
use OSS\Signer\Signer;
use OSS\Signer\SignerV1;
use OSS\Signer\SigningContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Object Storage Service(OSS)'s client class, which wraps all OSS APIs user could call to talk to OSS.
 * Users could do operations on bucket, object, including MultipartUpload or setting ACL via an OSSClient instance.
 * For more details, please check out the OSS API document:https://www.alibabacloud.com/help/doc-detail/31947.htm
 *
 * @method \OSS\Result optionsObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise optionsObjectAsync(array $args = [])
 * @method \OSS\Result openMetaQuery(array $args = [])
 * @method \GuzzleHttp\Promise\Promise openMetaQueryAsync(array $args = [])
 * @method \OSS\Result doMetaQuery(array $args = [])
 * @method \GuzzleHttp\Promise\Promise doMetaQueryAsync(array $args = [])
 * @method \OSS\Result closeMetaQuery(array $args = [])
 * @method \GuzzleHttp\Promise\Promise closeMetaQueryAsync(array $args = [])
 * @method \OSS\Result updateUserAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateUserAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result updateBucketAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateBucketAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result abortMultipartUpload(array $args = [])
 * @method \GuzzleHttp\Promise\Promise abortMultipartUploadAsync(array $args = [])
 * @method \OSS\Result abortBucketWorm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise abortBucketWormAsync(array $args = [])
 * @method \OSS\Result appendObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise appendObjectAsync(array $args = [])
 * @method \OSS\Result completeMultipartUpload(array $args = [])
 * @method \GuzzleHttp\Promise\Promise completeMultipartUploadAsync(array $args = [])
 * @method \OSS\Result completeBucketWorm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise completeBucketWormAsync(array $args = [])
 * @method \OSS\Result extendBucketWorm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise extendBucketWormAsync(array $args = [])
 * @method \OSS\Result copyObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise copyObjectAsync(array $args = [])
 * @method \OSS\Result createBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBucketAsync(array $args = [])
 * @method \OSS\Result createCnameToken(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createCnameTokenAsync(array $args = [])
 * @method \OSS\Result createAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createAccessPointAsync(array $args = [])
 * @method \OSS\Result initiateMultipartUpload(array $args = [])
 * @method \GuzzleHttp\Promise\Promise initiateMultipartUploadAsync(array $args = [])
 * @method \OSS\Result initiateBucketWorm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise initiateBucketWormAsync(array $args = [])
 * @method \OSS\Result initUserAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise initUserAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result initBucketAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise initBucketAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result deleteBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketAsync(array $args = [])
 * @method \OSS\Result deleteBucketLogging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketLoggingAsync(array $args = [])
 * @method \OSS\Result deleteBucketCors(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketCorsAsync(array $args = [])
 * @method \OSS\Result deleteBucketEncryption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketEncryptionAsync(array $args = [])
 * @method \OSS\Result deleteBucketInventory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketInventoryAsync(array $args = [])
 * @method \OSS\Result deleteBucketLifecycle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketLifecycleAsync(array $args = [])
 * @method \OSS\Result deleteBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketPolicyAsync(array $args = [])
 * @method \OSS\Result deleteBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketReplicationAsync(array $args = [])
 * @method \OSS\Result deleteBucketTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketTagsAsync(array $args = [])
 * @method \OSS\Result deleteBucketWebsite(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteBucketWebsiteAsync(array $args = [])
 * @method \OSS\Result deleteAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointAsync(array $args = [])
 * @method \OSS\Result deleteAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAccessPointPolicyAsync(array $args = [])
 * @method \OSS\Result deleteCname(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteCnameAsync(array $args = [])
 * @method \OSS\Result deleteStyle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteStyleAsync(array $args = [])
 * @method \OSS\Result deleteLiveChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteLiveChannelAsync(array $args = [])
 * @method \OSS\Result deleteObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteObjectAsync(array $args = [])
 * @method \OSS\Result deleteMultipleObjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteMultipleObjectsAsync(array $args = [])
 * @method \OSS\Result deleteObjectTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteObjectTaggingAsync(array $args = [])
 * @method \OSS\Result deleteObjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteObjectsAsync(array $args = [])
 * @method \OSS\Result getBucketInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketInfoAsync(array $args = [])
 * @method \OSS\Result getBucketLocation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketLocationAsync(array $args = [])
 * @method \OSS\Result getBucketStat(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketStatAsync(array $args = [])
 * @method \OSS\Result getBucketAcl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketAclAsync(array $args = [])
 * @method \OSS\Result getBucketTransferAcceleration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketTransferAccelerationAsync(array $args = [])
 * @method \OSS\Result getBucketWorm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketWormAsync(array $args = [])
 * @method \OSS\Result getAccessPoint(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointAsync(array $args = [])
 * @method \OSS\Result getCnameToken(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCnameTokenAsync(array $args = [])
 * @method \OSS\Result getBucketCors(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketCorsAsync(array $args = [])
 * @method \OSS\Result getBucketAccessMonitor(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketAccessMonitorAsync(array $args = [])
 * @method \OSS\Result getBucketEncryption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketEncryptionAsync(array $args = [])
 * @method \OSS\Result getBucketInventory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketInventoryAsync(array $args = [])
 * @method \OSS\Result getBucketLifecycle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketLifecycleAsync(array $args = [])
 * @method \OSS\Result getBucketLogging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketLoggingAsync(array $args = [])
 * @method \OSS\Result getBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketPolicyAsync(array $args = [])
 * @method \OSS\Result getBucketPolicyStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketPolicyStatusAsync(array $args = [])
 * @method \OSS\Result getBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketReplicationAsync(array $args = [])
 * @method \OSS\Result getBucketReplicationLocation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketReplicationLocationAsync(array $args = [])
 * @method \OSS\Result getBucketReplicationProgress(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketReplicationProgressAsync(array $args = [])
 * @method \OSS\Result getBucketResourceGroup(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketResourceGroupAsync(array $args = [])
 * @method \OSS\Result getBucketRequestPayment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketRequestPaymentAsync(array $args = [])
 * @method \OSS\Result getBucketTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketTagsAsync(array $args = [])
 * @method \OSS\Result getBucketVersioning(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketVersioningAsync(array $args = [])
 * @method \OSS\Result getBucketWebsite(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketWebsiteAsync(array $args = [])
 * @method \OSS\Result getBucketReferer(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBucketRefererAsync(array $args = [])
 * @method \OSS\Result getAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessPointPolicyAsync(array $args = [])
 * @method \OSS\Result getMetaQueryStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMetaQueryStatusAsync(array $args = [])
 * @method \OSS\Result getUserAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getUserAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result getStyle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getStyleAsync(array $args = [])
 * @method \OSS\Result getSymlink(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSymlinkAsync(array $args = [])
 * @method \OSS\Result getLiveChannelInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLiveChannelInfoAsync(array $args = [])
 * @method \OSS\Result getLiveChannelStat(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLiveChannelStatAsync(array $args = [])
 * @method \OSS\Result getLiveChannelHistory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLiveChannelHistoryAsync(array $args = [])
 * @method \OSS\Result getVodPlaylist(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getVodPlaylistAsync(array $args = [])
 * @method \OSS\Result getObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectAsync(array $args = [])
 * @method \OSS\Result getObjectMeta(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectMetaAsync(array $args = [])
 * @method \OSS\Result getObjectAcl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectAclAsync(array $args = [])
 * @method \OSS\Result getObjectAttributes(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectAttributesAsync(array $args = [])
 * @method \OSS\Result getObjectLegalHold(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectLegalHoldAsync(array $args = [])
 * @method \OSS\Result getObjectLockConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectLockConfigurationAsync(array $args = [])
 * @method \OSS\Result getObjectRetention(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectRetentionAsync(array $args = [])
 * @method \OSS\Result getObjectTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectTaggingAsync(array $args = [])
 * @method \OSS\Result getObjectTorrent(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getObjectTorrentAsync(array $args = [])
 * @method \OSS\Result getPublicAccessBlock(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getPublicAccessBlockAsync(array $args = [])
 * @method \OSS\Result headBucket(array $args = [])
 * @method \GuzzleHttp\Promise\Promise headBucketAsync(array $args = [])
 * @method \OSS\Result headObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise headObjectAsync(array $args = [])
 * @method \OSS\Result listBucketInventory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBucketInventoryAsync(array $args = [])
 * @method \OSS\Result listBuckets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBucketsAsync(array $args = [])
 * @method \OSS\Result listBucketAntiDDosInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBucketAntiDDosInfoAsync(array $args = [])
 * @method \OSS\Result listMultipartUploads(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listMultipartUploadsAsync(array $args = [])
 * @method \OSS\Result listCname(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCnameAsync(array $args = [])
 * @method \OSS\Result listStyle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listStyleAsync(array $args = [])
 * @method \OSS\Result listObjectVersions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listObjectVersionsAsync(array $args = [])
 * @method \OSS\Result listObjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listObjectsAsync(array $args = [])
 * @method \OSS\Result listObjectsV2(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listObjectsV2Async(array $args = [])
 * @method \OSS\Result listParts(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPartsAsync(array $args = [])
 * @method \OSS\Result listAccessPoints(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAccessPointsAsync(array $args = [])
 * @method \OSS\Result listLiveChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listLiveChannelAsync(array $args = [])
 * @method \OSS\Result putBucketAcl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketAclAsync(array $args = [])
 * @method \OSS\Result putBucketTransferAcceleration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketTransferAccelerationAsync(array $args = [])
 * @method \OSS\Result putBucketCors(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketCorsAsync(array $args = [])
 * @method \OSS\Result putBucketEncryption(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketEncryptionAsync(array $args = [])
 * @method \OSS\Result putBucketInventory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketInventoryAsync(array $args = [])
 * @method \OSS\Result putBucketLifecycle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketLifecycleAsync(array $args = [])
 * @method \OSS\Result putBucketLogging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketLoggingAsync(array $args = [])
 * @method \OSS\Result putBucketPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketPolicyAsync(array $args = [])
 * @method \OSS\Result putBucketAccessMonitor(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketAccessMonitorAsync(array $args = [])
 * @method \OSS\Result putBucketReferer(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketRefererAsync(array $args = [])
 * @method \OSS\Result putBucketReplication(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketReplicationAsync(array $args = [])
 * @method \OSS\Result putBucketResourceGroup(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketResourceGroupAsync(array $args = [])
 * @method \OSS\Result putBucketRTC(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketRTCAsync(array $args = [])
 * @method \OSS\Result putBucketRequestPayment(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketRequestPaymentAsync(array $args = [])
 * @method \OSS\Result putBucketTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketTagsAsync(array $args = [])
 * @method \OSS\Result putBucketVersioning(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketVersioningAsync(array $args = [])
 * @method \OSS\Result putBucketWebsite(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putBucketWebsiteAsync(array $args = [])
 * @method \OSS\Result putAccessPointPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putAccessPointPolicyAsync(array $args = [])
 * @method \OSS\Result putCname(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putCnameAsync(array $args = [])
 * @method \OSS\Result putStyle(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putStyleAsync(array $args = [])
 * @method \OSS\Result putSymlink(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putSymlinkAsync(array $args = [])
 * @method \OSS\Result putLiveChannel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putLiveChannelAsync(array $args = [])
 * @method \OSS\Result putLiveChannelStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putLiveChannelStatusAsync(array $args = [])
 * @method \OSS\Result putObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putObjectAsync(array $args = [])
 * @method \OSS\Result putObjectAcl(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putObjectAclAsync(array $args = [])
 * @method \OSS\Result putObjectTagging(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putObjectTaggingAsync(array $args = [])
 * @method \OSS\Result putPublicAccessBlock(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putPublicAccessBlockAsync(array $args = [])
 * @method \OSS\Result postVodPlaylist(array $args = [])
 * @method \GuzzleHttp\Promise\Promise postVodPlaylistAsync(array $args = [])
 * @method \OSS\Result restoreObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise restoreObjectAsync(array $args = [])
 * @method \OSS\Result selectObject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise selectObjectAsync(array $args = [])
 * @method \OSS\Result uploadPart(array $args = [])
 * @method \GuzzleHttp\Promise\Promise uploadPartAsync(array $args = [])
 * @method \OSS\Result uploadPartCopy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise uploadPartCopyAsync(array $args = [])
 */
class OssClient
{
    use OssClientTrait;
    // Domain Types
    const OSS_HOST_TYPE_NORMAL = "normal";//http://bucket.oss-cn-hangzhou.aliyuncs.com/object
    const OSS_HOST_TYPE_IP = "ip";  //http://1.1.1.1/bucket/object
    const OSS_HOST_TYPE_SPECIAL = 'special'; //http://bucket.guizhou.gov/object
    const OSS_HOST_TYPE_CNAME = "cname";  //http://mydomain.com/object
    const OSS_HOST_TYPE_PATH_STYLE = "path_style";  //http://oss-cn-hangzhou.aliyuncs.com/bucket/object
    const OSS_SUB_RESOURCE = 'sub_resource';
    const OSS_CONTENT_TYPE = 'Content-Type';
    const OSS_CONTENT_LENGTH = 'Content-Length';
    const OSS_USER_AGENT = 'User-Agent';

    const DEFAULT_CONTENT_TYPE = 'application/octet-stream';
    //use ssl flag
    private $useSSL = false;
    private $maxRetries = 3;
    private $redirects = 0;

    /**
     * @var CredentialsProvider
     */
    private $provider;

    /**
     * @var bool|string
     */
    private $hostname;


    /**
     * @var bool
     */
    private $isCname = false;

    /**
     * @var string cn-hangzhou
     */
    private $region;

    /**
     * @var Signer|SignerV1
     */
    private $signer;

    // user's domain type. It could be one of the four: OSS_HOST_TYPE_NORMAL, OSS_HOST_TYPE_IP, OSS_HOST_TYPE_SPECIAL, OSS_HOST_TYPE_CNAME
    private $hostType = self::OSS_HOST_TYPE_NORMAL;

    /**
     * @var Client
     */
    private $httpClient;
    private $timeout = 60;
    private $connectTimeout = 30;
    private $readTimeout = 30;

    /**
     * The connection timeout time, which is 10 seconds by default
     *
     * @var int
     */
    public $connect_timeout = 10;

    // OssClient version information
    const OSS_NAME = "aliyun-sdk-php";
    const OSS_VERSION = "3.0.0";


    /**
     * OssClient constructor.
     */
    public function __construct(array $config)
    {
        $region = isset($config['region']) ? $config['region'] : '';
        $endpoint = isset($config['endpoint']) ? $config['endpoint'] : '';
        $provider = isset($config['provider']) ? $config['provider'] : '';
        if (empty($endpoint)) {
            throw new OssException("endpoint is empty");
        }
        if(!$provider instanceof CredentialsProvider){
            throw new OssException("provider must be an instance of CredentialsProvider");
        }
        self::checkEnv();
        if (isset($config['maxRetries'])){
            $this->maxRetries = intval($config['maxRetries']);
        }
        $this->region = $region;
        $this->provider = $provider;
        $this->hostname = $this->resolveEndpoint($endpoint);
        $this->resolveHttpClient();
        $this->resolveSigner();
    }

    /**
     * Checks endpoint type and returns the endpoint without the protocol schema.
     * Figures out the domain's type (ip, cname or private/public domain).
     *
     * @param string $endpoint
     * @return string The domain name without the protocol schema.
     * @throws OssException
     */
    private function resolveEndpoint($endpoint)
    {
        if (strpos($endpoint, 'http://') === 0) {
            $ret_endpoint = substr($endpoint, strlen('http://'));
        } elseif (strpos($endpoint, 'https://') === 0) {
            $ret_endpoint = substr($endpoint, strlen('https://'));
            $this->useSSL = true;
        } else {
            $ret_endpoint = $endpoint;
        }

        if ($this->isCname) {
            $this->hostType = self::OSS_HOST_TYPE_CNAME;
        } elseif (OssUtil::isIPFormat($ret_endpoint)) {
            $this->hostType = self::OSS_HOST_TYPE_IP;
        } else {
            $this->hostType = self::OSS_HOST_TYPE_NORMAL;
        }

        return OssUtil::getHostPortFromEndpoint($ret_endpoint);
    }

    /**
     * Resolve HttpClient
     */
    private function resolveHttpClient()
    {
        $handler = HandlerStack::create();
        $handler->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        $handler->push(Middleware::mapRequest(function (RequestInterface $request) {
            return $request->withHeader('User-Agent', $this->generateUserAgent());
        }));
        $handler->push($this::handleErrors());
        if ($this->httpClient == null){
            $this->httpClient = new Client(
                [
                    'handler' => $handler,
                    'timeout' => $this->timeout,
                    'read_timeout' => $this->readTimeout,
                    'connect_timeout' => $this->connectTimeout,
                    'allow_redirects' => false,
                    'verify' => $this->useSSL,
                    'expect' => false,
                ]
            );
        }

    }

    /**
     * @param $name
     * @param $options
     * @return PromiseInterface|Result
     * @throws GuzzleException
     */
    public function __call($name,$options){
        try {
            $options = isset($options[0]) ? $options[0] : [];
            if (empty($options['method'])){
                throw new OssException('method must be a non-empty string.');
            }
            $operationName = $name;
            $bucket = isset($options['bucket']) ? $options['bucket'] : null;
            $key = isset($options['key']) ? $options['key'] : null;
            $method = $options['method'];
            $headers = isset($options['headers']) ? $options['headers'] : null;
            $parameters = isset($options['parameters']) ? $options['parameters'] : null;
            $body = isset($options['body']) ? $options['body'] : null;
            $metadata = isset($options['metadata']) ? $options['metadata'] : null;
            if (isset($body) && is_array($body)){
                $body = OssUtil::arrayToXml($body);
            }
            $input = new OperationInput($operationName,$bucket,$key,$method,$headers,$parameters,$body,$metadata);
            return $this->invokeOperation($input);
        }catch (OssException $e){
            throw $e;
        }
    }

    /**
     * Resolve Signer
     */
    private function resolveSigner(){
        if ($this->signer == null){
            $this->signer = new SignerV1();
        }
    }

    /**
     * @param OperationInput $input
     * @return PromiseInterface
     * @throws OssException|GuzzleException
     */
    public function invokeOperation(OperationInput $input)
    {
        return $this->sendRequest($input);
    }

    /**
     * @param OperationInput $input
     * @return PromiseInterface|Result
     * @throws OssException|GuzzleException
     */
    public function sendRequest(OperationInput $input)
    {
        // host & path
        $hostName = $this->generateHostname($input->getBucket());
        $resource_uri = $this->generateResourceUri($input);
        $scheme = $this->useSSL ? 'https://' : 'http://';
        $strUrl = sprintf("%s%s%s", $scheme, $hostName, $resource_uri);
        // querys
        if (!empty($input->getParameters())) {
            $query = http_build_query($input->getParameters());
            $strUrl .= "?" . $query;
        }
        $request = new Request($input->getMethod(), $strUrl);
        // headers
        if ($input->getHeaders() != null){
            foreach ($input->getHeaders() as $k => $v) {
                if (!empty($k) && !empty($v)) {
                    $request = $request->withHeader($k, $v);
                }
            }
        }
        $body = Utils::streamFor($input->getBody());
        $len = $body->getSize();
        if ($len >= 0 && !$request->hasHeader(self::OSS_CONTENT_LENGTH)) {
            $request = $request->withHeader(self::OSS_CONTENT_LENGTH, $len);
        }
        if (!$request->hasHeader(self::OSS_CONTENT_TYPE)){
            $request = $request->withHeader(self::OSS_CONTENT_TYPE, $this->getMimeType($input->getKey()));
        }
        $request = $request->withBody($body);
        // signing context
        $meta = $input->getMetadata();
        $subResource = null;
        if (isset($meta)){
            $subResource = isset($meta[Signer::SUB_RESOURCE]) ? $meta[Signer::SUB_RESOURCE] : null;
        }
        $cred = $this->provider->getCredentials();
        $signingCtx = new SigningContext("oss",$this->region,$input->getBucket(),$input->getKey(),$request,$subResource,$cred);
        $signingCtx = $this->signer->sign($signingCtx);
        $method = $input->getOperationName();
        if (substr($method, -5) === 'Async') {
            return $this->httpClient->sendAsync($signingCtx->getRequest())->then(
                function(ResponseInterface $response) use ($request,$input){
                    return $this->responseToResultTransformer($request,$response,$input);
                }
            );
        } else {
            $response = $this->httpClient->send($signingCtx->getRequest());
            return $this->responseToResultTransformer($request,$response,$input);
        }
    }


    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param OperationInput $input
     * @return Result
     */
    private function responseToResultTransformer(RequestInterface $request,ResponseInterface $response,OperationInput $input)
    {
        $result = new Result();
        $transformer = new ResultTransformer($input,$result);
        $result = $transformer->headerTransformer($request,$response,$result);
        return $transformer->bodyTransformer($response,$result);
    }


    /**
     * @param OperationInput $input
     * @return string
     */
    private function generateResourceUri(OperationInput $input) {
        $resource_uri = "";
        $bucket = $input->getBucket();
        $object = $input->getKey();

        if ($bucket == "") {
            if ($this->hostType === self::OSS_HOST_TYPE_IP) {
                $resource_uri = $this->hostname . '/' . $bucket;
            }
        }
        // resource_uri + object
        if (isset($object) && '/' !== $object) {
            $resource_uri .= '/' . str_replace(array('%2F', '%25'), array('/', '%'), rawurlencode($input->getKey()));
        }
        return $resource_uri;
    }

    /**
     * Gets the host name for the current request.
     * It could be either a third level domain (prefixed by bucket name) or second level domain if it's CName or IP
     *
     * @param $bucket
     * @return string The host name without the protocol scheem (e.g. https://)
     */
    private function generateHostname($bucket)
    {
        if ($this->hostType === self::OSS_HOST_TYPE_IP || $this->hostType === self::OSS_HOST_TYPE_PATH_STYLE ) {
            $hostname = $this->hostname;
        } elseif ($this->hostType === self::OSS_HOST_TYPE_CNAME) {
            $hostname = $this->hostname;
        } else {
            // Private domain or public domain
            $hostname = ($bucket == '') ? $this->hostname : ($bucket . '.') . $this->hostname;
        }
        return $hostname;
    }

    /**
     * retryDecider
     * @return \Closure
     */
    protected function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            ResponseInterface $response = null,
            OssException $exception = null
        ) {
            if ($retries >= $this->maxRetries) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @return \Closure
     */
    protected function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    /**
     * Generates UserAgent
     *
     * @return string
     */
    private function generateUserAgent()
    {
        return self::OSS_NAME . "/" . self::OSS_VERSION . " (" . php_uname('s') . "/" . php_uname('r') . "/" . php_uname('m') . ";" . PHP_VERSION . ")";
    }

    /**
     * Gets mimetype
     *
     * @param string $object
     * @return string
     */
    private function getMimeType($object, $file = null)
    {
        if (!is_null($file)) {
            $type = MimeTypes::getMimetype($file);
            if (!is_null($type)) {
                return $type;
            }
        }

        $type = MimeTypes::getMimetype($object);
        if (!is_null($type)) {
            return $type;
        }

        return self::DEFAULT_CONTENT_TYPE;
    }

    /**
     * Capture error information
     * @return \Closure
     */
    public static function handleErrors() {
        return function (callable $handler) {
            return new ExceptionMiddleware($handler);
        };
    }

    /**
     * Check if all dependent extensions are installed correctly.
     * For now only "curl" is needed.
     * @throws OssException
     */
    public static function checkEnv(){
        if (function_exists('get_loaded_extensions')) {
            //Test curl extension
            $enabled_extension = array("curl");
            $extensions = get_loaded_extensions();
            if ($extensions) {
                foreach ($enabled_extension as $item) {
                    if (!in_array($item, $extensions)) {
                        throw new OssException("Extension {" . $item . "} is not installed or not enabled, please check your php env.");
                    }
                }
            } else {
                throw new OssException("Function get_loaded_extensions not found.");
            }
        } else {
            throw new OssException('Function get_loaded_extensions has been disabled, please check php config.');
        }
    }


    public function __destruct(){}
}