<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

#[XmlRoot(name: 'Owner')]
final class Owner extends Model
{
    #[XmlElement(rename: 'ID', type: 'string')]
    public ?string $id;

    #[XmlElement(rename: 'DisplayName', type: 'string')]
    public ?string $displayName;

    public function __construct(
        ?string $id = null,
        ?string $displayName = null,
    ) {
        $this->id = $id;
        $this->displayName = $displayName;
    }
}
