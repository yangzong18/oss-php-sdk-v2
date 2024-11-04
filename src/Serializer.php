<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\Functions;

final class Serializer
{
    public static function serializeXml(Model $model, string $root = ''): string
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'utf8');
        self::serializeXmlModel($writer, $root,$model);
        $writer->endDocument();
        return $writer->flush();
    }

    private static function serializeXmlAny(\XMLWriter $writer, string $name, mixed $value)
    {
        if ($value instanceof Model) {
            return self::serializeXmlModel($writer, $name, $value);
        }

        // time format

        // enum

        // bool
        $type = \gettype($value);
        if (in_array(needle: $type, haystack: ['bool', 'boolean'])) {
            $writer->startElement($name);
            $writer->text($value === true ? 'true': 'false');
            $writer->endElement();
            return;
        }
 
        // other primitive type
        if (in_array(needle: $type, haystack: ['int', 'integer', 'float', 'double', 'string'])) {
            $writer->startElement($name);
            $writer->text((string)$value);
            $writer->endElement();
            return;
        }

        throw new \Exception("Serialization Error, Unsupport type " . gettype($value));
    }

    private static function serializeXmlModel(\XMLWriter $writer, string $name, Model $value)
    {
        $ro = new \ReflectionObject($value);

        if (empty($name)) {
            $annotation = Functions::getXmlRootAnnotation($ro);
            if ($annotation != null) {
                $name = $annotation->name;
            }
            if (empty($name)) {
                $name = \get_class($value);
                if ($pos = \strrpos($name, '\\')) {
                    $name = \substr($name, $pos + 1);
                }
            }
        }

        // start element
        $writer->startElement($name);

        // children element
        foreach($ro->getProperties() as $property) {
            $val = $property->getValue($value);
            if (!isset($val)) {
                continue;
            }

            $annotation = Functions::getXmlElementAnnotation($property);
            if ($annotation == null) {
                continue;
            }

            if (is_array($val)) {
                foreach($val as $vval) {
                    self::serializeXmlAny($writer, $annotation->rename, $vval);
                }
            } else {
                self::serializeXmlAny($writer, $annotation->rename, $val);
            }
        }

        // end element
        $writer->endElement();
    }
}