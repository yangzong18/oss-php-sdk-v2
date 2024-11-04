<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class XmlElement implements AnnotationInterface
{
    public string $rename;

    public string $type;

    public function __construct(string $rename, string $type)
    {
        $this->rename = $rename;
        $this->type = $type;
    }
}
