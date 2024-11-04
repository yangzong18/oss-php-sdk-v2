<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Annotation;

#[\Attribute]
class TagProperty implements AnnotationInterface
{
    public string $tag;

    public string $position;

    public string $rename;

    public string $type;

    public function __construct(string $tag, string $position, string $rename, string $type)
    {
        $this->tag = $tag;
        $this->position = $position;
        $this->rename = $rename;
        $this->type = $type;
    }
}
