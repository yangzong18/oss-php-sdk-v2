<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Signer;

use DateTime;
use GuzzleHttp\Psr7\Query;

class SignerV1 implements SignerInterface
{

    /**
     * @var string[]
     */
    private static $requiredSignedParametersMap = [
        "acl",
        "bucketInfo",
        "location",
        "stat",
        "delete",
        "append",
        "tagging",
        "objectMeta",
        "uploads",
        "uploadId",
        "partNumber",
        "security-token",
        "position",
        "response-content-type",
        "response-content-language",
        "response-expires",
        "response-cache-control",
        "response-content-disposition",
        "response-content-encoding",
        "restore",
        "callback",
        "callback-var",
        "versions",
        "versioning",
        "versionId",
        "sequential",
        "continuation-token",
        "regionList",
        "cloudboxes",
        "symlink",
    ];


    /**
     * @param SigningContext $signingCtx
     */
    public function sign(SigningContext $signingCtx)
    {
        if ($signingCtx === null) {
            throw new \InvalidArgumentException("SigningContext is null.");
        }

        if ($signingCtx->credentials === null || !$signingCtx->credentials->hasKeys()) {
            throw new \InvalidArgumentException("SigningContext Credentials is null or empty.");
        }

        if ($signingCtx->request === null) {
            throw new \InvalidArgumentException("SigningContext Request is null.");
        }

        if ($signingCtx->authMethodQuery) {
            $this->authQuery($signingCtx);
        } else {
            $this->authHeader($signingCtx);
        }
    }


    /**
     * @param array $list
     * @param string $key
     * @return bool
     */
    private function isSubResource(array $list, string $key): bool
    {
        return in_array($key, $list);
    }

    /**
     * @param string $date
     * @param SigningContext $signingCtx
     * @return string
     */
    private function calcStringToSign(string $date, SigningContext $signingCtx): string
    {
        $request = $signingCtx->request;
        $contentMd5 = $request->getHeaderLine(self::CONTENT_MD5_HEADER);
        $contentType = $request->getHeaderLine(self::CONTENT_TYPE_HEADER);

        // CanonicalizedOSSHeaders
        $headers = [];
        foreach ($request->getHeaders() as $k => $v) {
            $lowerCaseKey = strtolower($k);
            if (str_starts_with($lowerCaseKey, self::OSS_HEADER_PREFIX)) {
                $headers[] = $lowerCaseKey;
            }
        }
        sort($headers);
        $headerItems = [];
        foreach ($headers as $k) {
            $headerValues = array_map('trim', $request->getHeader($k));
            $headerItems[] = $k . ':' . implode(',', $headerValues) . "\n";
        }
        $canonicalizedOSSHeaders = implode('', $headerItems);

        // CanonicalizedResource
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $params = [];
        foreach ($query as $k => $v) {
            if (in_array($k, self::$requiredSignedParametersMap) ||
                str_starts_with($k, self::OSS_HEADER_PREFIX) ||
                $this->isSubResource($signingCtx->subResource, $k)) {
                $params[] = ($v !== "") ? "$k=$v" : $k;
            }
        }
        sort($params);
        $subResource = implode('&', $params);
        $canonicalizedResource = '/' . ($signingCtx->bucket ? $signingCtx->bucket . '/' : '') .
            ($signingCtx->key ? $signingCtx->key : '') .
            ($subResource ? '?' . $subResource : '');

        // String to Sign
        return $request->getMethod() . "\n" .
            $contentMd5 . "\n" .
            $contentType . "\n" .
            $date . "\n" .
            $canonicalizedOSSHeaders .
            $canonicalizedResource;
    }


    /**
     * @param SigningContext $signingCtx
     */
    public function authHeader(SigningContext $signingCtx): void
    {
        $request = $signingCtx->request;
        $cred = $signingCtx->credentials;

        // Date
        if (!isset($signingCtx->time)) {
            $signingCtx->time = (new DateTime())->modify('+' . $signingCtx->clockOffset . 'seconds');
        }

        $datetime = $signingCtx->time->format(DATE_RFC7231);
        $request = $request->withHeader(self::DATE_HEADER, $datetime);

        // Credentials information
        if ($cred->getSecurityToken() !== '') {
            $request = $request->withHeader(self::SECURITY_TOKEN_HEADER, $cred->getSecurityToken());
        }

        // StringToSign
        $stringToSign = $this->calcStringToSign($datetime, $signingCtx);
        $signingCtx->stringToSign = $stringToSign;

        // Signature
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $cred->getAccessKeySecret(), true));

        // Authorization header
        $request = $request->withHeader(self::AUTHORIZATION_HEADER, sprintf("OSS %s:%s", $cred->getAccessKeyId(), $signature));
        $signingCtx->request = $request;
    }


    /**
     * @param SigningContext $signingCtx
     */
    public function authQuery(SigningContext $signingCtx): void
    {
        $request = $signingCtx->request;
        $cred = $signingCtx->credentials;

        // Date
        if (!isset($signingCtx->time)) {
            $signingCtx->time = (new DateTime())->modify('+' . self::DEFAULT_EXPIRES_DURATION . 'seconds');
        }
        $datetime = (string)$signingCtx->time->getTimestamp();

        // Credentials information
        $query = Query::parse($request->getUri()->getQuery());
        if ($cred->getSecurityToken() !== '') {
            $query[self::SECURITY_TOKEN_QUERY] = $cred->getSecurityToken();
            $request = $request->withUri($request->getUri()->withQuery(http_build_query($query)));
            $signingCtx->request = $request;
        }

        // StringToSign
        $stringToSign = $this->calcStringToSign($datetime, $signingCtx);
        $signingCtx->stringToSign = $stringToSign;

        // Signature
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $cred->getAccessKeySecret(), true));

        // Authorization query
        $query[self::EXPIRES_QUERY] = $datetime;
        $query[self::ACCESS_KEY_ID_QUERY] = $cred->getAccessKeyId();
        $query[self::SIGNATURE_QUERY] = $signature;
        ksort($query);
        $request = $request->withUri($request->getUri()->withQuery(http_build_query($query)));
        $signingCtx->request = $request;
    }


    /**
     * @param string $h
     * @return bool
     */
    public function isSignedHeader(string $h): bool
    {
        $lowerCaseKey = strtolower($h);
        return (str_starts_with($lowerCaseKey, self::OSS_HEADER_PREFIX)) ||
            ($lowerCaseKey === "date") ||
            ($lowerCaseKey === "content-type") ||
            ($lowerCaseKey === "content-md5");
    }
}