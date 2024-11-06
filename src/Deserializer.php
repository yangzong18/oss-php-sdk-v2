<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

use AlibabaCloud\Oss\V2\OperationOutput;
use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Types\ResultModel;
use AlibabaCloud\Oss\V2\Annotation\Functions;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Exception\DeserializationExecption;
use AlibabaCloud\Oss\V2\Utils;

final class Deserializer
{
    public static function deserializeXml(string $value, mixed $className, string $expect = ''): Model
    {
        $xml = simplexml_load_string($value);

        if (false === $xml) {
            throw new DeserializationExecption('simplexml_load_string returns false');
        }

        //check expect root name
        if (
            !empty($xml) &&
            !empty($expect) &&
            !str_contains($value, '<' . $expect . '>')
        ) {
            throw new DeserializationExecption('Not found tag <' . $expect . '>');
        }

        return self::deserializeXmlModel($xml, $className);
    }

    private static function deserializeXmlAny(\SimpleXMLElement $element, string $type, XmlElement $annotation)
    {
        $values = [];
        foreach ($element as $item) {
            switch ($annotation->type) {
                case "bool":
                    $vv = self::castToBool($item->__toString());
                    break;
                case "string":
                    $vv = $item->__toString();
                    break;
                case "int":
                    $vv = self::castToInt($item->__toString());
                    break;
                case "float":
                    $vv = self::castToFloat($item->__toString());
                    break;
                case "DateTime":
                    $vv = self::castToDatetime($item->__toString(), $annotation->format);
                    break;
                default:
                    $vv = self::deserializeXmlModel($item, $annotation->type);
            }
            array_push($values, $vv);
        }

        if (in_array(needle: $type, haystack: ['?array', 'array'])) {
            return $values;
        }
        return $values[0];
    }

    private static function deserializeXmlModel(\SimpleXMLElement $element, mixed $class)
    {
        if (\is_object($class)) {
            if (!($class instanceof Model)) {
                throw new DeserializationExecption($class . " is not subclass of Model");
            }
            $rc = new \ReflectionObject($class);
            $obj = $class;
        } else {
            $rc = new \ReflectionClass($class);
            $obj = $rc->newInstance();
            if (!($obj instanceof Model)) {
                throw new DeserializationExecption($class . " is not subclass of Model");
            }
        }

        // children element
        foreach ($rc->getProperties() as $property) {
            $annotation = Functions::getXmlElementAnnotation($property);
            if ($annotation == null) {
                continue;
            }

            $name = $annotation->rename;
            if (!isset($element->$name)) {
                continue;
            }

            $type = (string)$property->getType();
            $value = self::deserializeXmlAny($element->$name, $type, $annotation);
            $property->setValue($obj, $value);
        }

        return $obj;
    }

    private static function castToBool(string $value): bool
    {
        $v = strtolower($value);
        if ('true' === $v || '1' === $v) {
            return true;
        } elseif ('false' === $v || '0' === $v) {
            return false;
        } else {
            throw new DeserializationExecption(
                sprintf('Could not convert data to boolean. Expected "true", "false", "1" or "0", but got %s.', $value)
            );
        }
    }

    private static function castToInt(string $value): int
    {
        return (int) $value;
    }

    private static function castToFloat(string $value): float
    {
        return (float) $value;
    }

    private static function castToDatetime(string $value, ?string $format): \DateTime
    {
        switch ($format) {
            case 'httptime':
                return \DateTime::createFromFormat(
                    'D, d M Y H:i:s \G\M\T',
                    $value,
                    new \DateTimeZone('UTC')
                );
            case 'unixtime':
                $datetime = new \DateTime();
                $datetime->setTimestamp((int)$value);
                return $datetime;
        }
        return \DateTime::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $value,
            new \DateTimeZone('UTC')
        );
    }
}
