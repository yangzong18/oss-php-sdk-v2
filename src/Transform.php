<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use AlibabaCloud\Oss\V2\Types\RequestModel;
use AlibabaCloud\Oss\V2\Models;

final class Transform
{
    public static function addContentType(RequestModel $request, OperationInput $input)
    {
        if ($input->hasHeader('Content-Type')) {
            return;
        }
        $value = Utils::guessContentType($input->getKey());
        if ($value !== '') {
            $input->setHeader('Content-Type', $value);
        }
    }

    public static function addContentMd5(RequestModel $request, OperationInput $input)
    {
        if ($input->hasHeader('Content-MD5')) {
            return;
        }

        $value = '1B2M2Y8AsgTpgAmY7PhCfg==';
        if ($input->getBody() != null) {
            $value = Utils::calcContentMd5($input->getBody());
        }
        $input->setHeader('Content-MD5', $value);
    }

    # bucket acl
    public static function fromGetBucketAcl(Models\GetBucketAclRequest $request): OperationInput
    {
        $input = new OperationInput(
            opName: 'GetBucketAcl',
            method: 'GET',
            parameters: ['acl' => ''],
            bucket: $request->bucket,
            opMetadata: ['sub-resource' => ['acl']]
        );

        return Serializer::serializeInput(
            $request,
            $input,
            [
                [self::class, 'addContentMd5']
            ]
        );
    }

    public static function toGetBucketAcl(OperationOutput $output): Models\GetBucketAclResult
    {
        $result = new Models\GetBucketAclResult();
        $customDeserializer = [
            [Deserializer::class, 'deserializeOutputInnerBody'],
        ];
        Deserializer::deserializeOutput($result, $output, $customDeserializer);
        return $result;
    }
}
