<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use AlibabaCloud\Oss\V2\Models;

final class Transform
{
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

        return Serializer::serializeInput($request, $input);
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
