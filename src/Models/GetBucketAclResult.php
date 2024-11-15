<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\ResultModel;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

#[XmlRoot(name: 'AccessControlPolicy')]
final class GetBucketAclResult extends ResultModel
{
    #[XmlElement(rename: 'Owner', type: Owner::class)]
    public ?Owner $owner;

    #[XmlElement(rename: 'AccessControlList', type: AccessControlList::class)]
    public ?AccessControlList $accessControlList;

    public function __construct(
        ?string $owner = null,
        ?string $accessControlList = null,
    ) {
        $this->owner = $owner;
        $this->owner = $accessControlList;
    }
}
