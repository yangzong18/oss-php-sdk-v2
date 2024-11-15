<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

#[XmlRoot(name: 'AccessControlList')]
final class AccessControlList extends Model
{
    #[XmlElement(rename: 'Grant', type: 'string')]
    public ?string $grant;

    public function __construct(
        ?string $grant = null,
    ) {
        $this->grant = $grant;
    }
}
