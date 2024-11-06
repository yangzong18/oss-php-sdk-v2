<?php

namespace UnitTests\Fixtures;

use AlibabaCloud\Oss\V2\Types\ModelTrait;
use AlibabaCloud\Oss\V2\Types\RequestModel;
use AlibabaCloud\Oss\V2\Annotation\TagProperty;
use AlibabaCloud\Oss\V2\Annotation\RequiredProperty;

class PutApiRequest  extends RequestModel
{
    use ModelTrait;

    #[RequiredProperty()]
    #[TagProperty(tag: 'input', position: 'host', rename: '', type: 'string')]
    private ?string $bucket;

    #[RequiredProperty()]
    #[TagProperty(tag: 'input', position: 'path', rename: '', type: 'string')]
    private ?string $key;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-str', type: 'string')]
    private ?string $strHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-int', type: 'int')]
    private ?int $intHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-bool', type: 'bool')]
    private ?bool $boolHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-float', type: 'float')]
    private ?float $floatHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-isotime', type: 'Datetime')]
    private ?\Datetime $isotimeHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-httptime', type: 'Datetime', format:'httptime')]
    private ?\Datetime $httptimeHeader;

    #[TagProperty(tag: 'input', position: 'header', rename: 'x-oss-unixtime', type: 'Datetime', format:'unixtime')]
    private ?\Datetime $unixtimeHeader;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-str', type: 'string')]
    private ?string $strParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-int', type: 'int')]
    private ?int $intParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-bool', type: 'bool')]
    private ?bool $boolParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-float', type: 'float')]
    private ?float $floatParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-isotime', type: 'Datetime')]
    private ?\Datetime $isotimeParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-httptime', type: 'Datetime', format:'httptime')]
    private ?\Datetime $httptimeParam;

    #[TagProperty(tag: 'input', position: 'query', rename: 'param-unixtime', type: 'Datetime', format:'unixtime')]
    private ?\Datetime $unixtimeParam;

    #[TagProperty(tag: 'input', position: 'body', rename: 'Configuration', type: 'xml')]
    private ?RootConfiguration $configuration;

    public function __construct(
        ?string $bucket = null,
        ?string $key = null,
        ?string $strHeader = null,
        ?int $intHeader = null,
        ?bool $boolHeader = null,
        ?float $floatHeader = null,
        ?\Datetime $isotimeHeader = null,
        ?\Datetime $httptimeHeader = null,
        ?\Datetime $unixtimeHeader = null,
        ?string $strParam = null,
        ?int $intParam = null,
        ?bool $boolParam = null,
        ?float $floatParam = null,
        ?\Datetime $isotimeParam = null,
        ?\Datetime $httptimeParam = null,
        ?\Datetime $unixtimeParam = null,
        ?RootConfiguration $configuration = null,
    ) {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->strHeader = $strHeader;
        $this->intHeader = $intHeader;
        $this->boolHeader = $boolHeader;
        $this->floatHeader = $floatHeader;
        $this->isotimeHeader = $isotimeHeader;
        $this->httptimeHeader = $httptimeHeader;
        $this->unixtimeHeader = $unixtimeHeader;
        $this->strParam = $strParam;
        $this->intParam = $intParam;
        $this->boolParam = $boolParam;
        $this->floatParam = $floatParam;
        $this->isotimeParam = $isotimeParam;
        $this->httptimeParam = $httptimeParam;
        $this->unixtimeParam = $unixtimeParam;
        $this->configuration = $configuration;
    }
}
