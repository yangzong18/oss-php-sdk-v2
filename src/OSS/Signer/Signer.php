<?php

namespace OSS\Signer;

interface Signer
{
    const SUB_RESOURCE = 'SubResource';
    const AUTHORIZATION_HEADER = 'Authorization';
    const SECURITY_TOKEN_HEADER = 'x-oss-security-token';
    const DATE_HEADER = 'Date';
    const CONTENT_TYPE_HEADER = 'Content-Type';
    const CONTENT_MD5_HEADER = 'Content-MD5';
    const OSS_HEADER_PREFIX = 'x-oss-';

    public function sign(SigningContext $signingCtx);
    public function preSign(SigningContext $signingCtx);
}
