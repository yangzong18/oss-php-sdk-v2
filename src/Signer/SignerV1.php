<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Signer;

use OSS\Utils\OssUtil;

class SignerV1 implements Signer
{
    public function sign(SigningContext $signingCtx)
    {
        $request = $signingCtx->getRequest();
        $cred = $signingCtx->getCredentials();
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $request = $request->withHeader(self::DATE_HEADER,$date);
        if ($cred->getSecurityToken() != "") {
            $request = $request->withHeader(self::SECURITY_TOKEN_HEADER,$cred->getSecurityToken());
        }
        $contentMd5 = $request->getHeader(self::CONTENT_MD5_HEADER);
        if (count($contentMd5) > 0){
            $contentMd5 = $contentMd5[0];
        }else{
            $contentMd5 = '';
        }
        $contentType = $request->getHeader(self::CONTENT_TYPE_HEADER);
        if (count($contentType) > 0){
            $contentType = $contentType[0];
        }else{
            $contentType = '';
        }
        //Canonicalized OSSHeaders
        $headers = [];
        foreach ($request->getHeaders() as $k => $v) {
            $lowerCaseKey = strtolower($k);
            if (strpos($lowerCaseKey, self::OSS_HEADER_PREFIX) === 0) {
                $headers[$lowerCaseKey] = '';
            }
        }
        ksort($headers);
        $headerItems = [];
        foreach ($headers as $k => $v) {
            $headerValues = array_map('trim', $request->getHeader($k));
            $headerItems[] = strtolower($k) . ":" . implode(",", $headerValues) . "\n";
        }
        $canonicalizedOSSHeaders = implode("", $headerItems);
        //Canonicalize Resource
        $query = $request->getUri()->getQuery();
        parse_str($query, $queryArray);
        $requiredSignedParameters = OssUtil::getRequiredParams();
        $params = [];
        foreach ($queryArray as $k => $v) {
            if (isset($requiredSignedParameters[$k])) {
                $params[$k] = '';
            } elseif (strpos($k, self::OSS_HEADER_PREFIX) === 0) {
                $params[$k] = '';
            } elseif ($this->isSubResource($signingCtx->getSubResource(), $k)) {
                $params[$k] = '';
            }
        }
        ksort($params);
        $paramItems = [];
        foreach ($params as $k => $val) {
            $v = $queryArray[$k];
            if (strlen($v) > 0) {
                $paramItems[] = $k . "=" . $v;
            } else {
                $paramItems[] = $k;
            }
        }
        $subResource = implode("&", $paramItems);
        if ($subResource != "") {
            $subResource = "?" . $subResource;
        }

        if ($signingCtx->getBucket() == "") {
            $canonicalizedResource = "/" . $signingCtx->getBucket() . $subResource;
        } else {
            $canonicalizedResource = "/" . $signingCtx->getBucket() . "/" . $signingCtx->getKey() . $subResource;
        }
        // String to Sign
        $stringToSign = $request->getMethod() . "\n" .
            $contentMd5 . "\n" .
            $contentType . "\n" .
            $date . "\n" .
            $canonicalizedOSSHeaders .
            $canonicalizedResource;
        // Sign
        $signature = base64_encode(hash_hmac("sha1", $stringToSign, $cred->getAccessKeySecret(), true));
        $authorizationStr = "OSS " . $cred->getAccessKeyId() . ":" . $signature;
        $request = $request->withHeader(self::AUTHORIZATION_HEADER,$authorizationStr);
        // Save sign info
        $signingCtx->setStringToSign($stringToSign);
        $signingCtx->setRequest($request);
        return $signingCtx;
    }

    public function preSign(SigningContext $signingCtx)
    {
        return null;
    }

    public function isSubResource($list, $key)
    {
        if (is_array($list)){
            return in_array($key, $list);
        }else{
            return false;
        }

    }
}