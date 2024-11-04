<?php

namespace UnitTests\Types;

use AlibabaCloud\Oss\V2\Types\Model;

class ModelA extends Model
{
    public ?string $strValue;

    public ?int $intValue;

    public ?bool $boolValue;

    public ?float $floatValue;

    public function __construct(
        ?string $strValue = null,
        ?int $intValue = null,
        ?bool $boolValue = null,
        ?float $floatValue = null,
    ) {
        $this->strValue = $strValue;
        $this->intValue = $intValue;
        $this->boolValue = $boolValue;
        $this->floatValue = $floatValue;
    }
}
