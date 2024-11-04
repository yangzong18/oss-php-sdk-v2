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

    private static function serializeXmlAny(\XMLWriter $writer, string $name, mixed $value, ?string $format)
    {
        if ($value instanceof Model) {
            self::serializeXmlModel($writer, $name, $value);
            return;
        }

        if ($value instanceof \DateTimeInterface) {
            $stamp = $value->getTimestamp();
            if ($format == null) {
                $format = 'iso8601';
            }
            switch($format) {
            case 'httptime':
                $tval = gmdate('D, d M Y H:i:s \G\M\T', $stamp);
                break;
            case 'unixtime':
                $tval = (string)$stamp;
                break;
            default:
                $tval = gmdate('Y-m-d\TH:i:s\Z', $stamp);
            }
            $writer->startElement($name);
            $writer->text($tval);
            $writer->endElement();
            return;
        }

        $type = \gettype($value);

        // bool
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


        // enum, since 8.1
        // TODO

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
                    self::serializeXmlAny($writer, $annotation->rename, $vval, $annotation->format);
                }
            } else {
                self::serializeXmlAny($writer, $annotation->rename, $val, $annotation->format);
            }
        }

        // end element
        $writer->endElement();
    }
}