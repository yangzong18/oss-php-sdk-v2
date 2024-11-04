<?php

namespace UnitTests\Types;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeListXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'MixedTypeXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'MixedTypeListXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeLackAnnotationXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'DatetimeTypeXml.php';


use AlibabaCloud\Oss\V2\Serializer;
use DateTime;
use UnitTests\Fixtures\BaiscTypeXml;
use UnitTests\Fixtures\BaiscTypeListXml;
use UnitTests\Fixtures\MixedTypeXml;
use UnitTests\Fixtures\MixedTypeListXml;
use UnitTests\Fixtures\BaiscTypeLackAnnotationXml;
use UnitTests\Fixtures\DatetimeTypeXml;


class SerializerTest extends \PHPUnit\Framework\TestCase
{
    public function testSerializeBaiscTypeXml()
    {
        // empty 
        $obj = new BaiscTypeXml();
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<BasicType/>', $str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(0, $xml->count());
        $this->assertFalse(isset($xml->StrValue));
        $this->assertFalse(isset($xml->IntValue));
        $this->assertFalse(isset($xml->BoolValue));
        $this->assertFalse(isset($xml->FloatValue));

        // all
        $obj = new BaiscTypeXml(
            strValue: 'str',
            intValue: 1234,
            boolValue: true,
            floatValue: 3.14,
        );
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(4, $xml->count());
        $this->assertEquals('str', $xml->StrValue);
        $this->assertEquals('1234', $xml->IntValue);
        $this->assertEquals('true', $xml->BoolValue);
        $this->assertEquals('3.14', $xml->FloatValue);

        // part
        $obj = new BaiscTypeXml(
            strValue: 'str-123',
            floatValue: 2.14,
        );
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(2, $xml->count());
        $this->assertEquals('str-123', $xml->StrValue);
        $this->assertFalse(isset($xml->IntValue));
        $this->assertFalse(isset($xml->BoolValue));
        $this->assertEquals('2.14', $xml->FloatValue);

        // part 2
        $obj = new BaiscTypeXml(
            strValue: 'str-123',
            boolValue: false,
        );
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(2, $xml->count());
        $this->assertEquals('str-123', $xml->StrValue);
        $this->assertFalse(isset($xml->IntValue));
        $this->assertEquals('false', $xml->BoolValue);
        $this->assertFalse(isset($xml->FloatValue));
    }

    public function testSerializeBaiscTypeListXml()
    {
        // empty
        $obj = new BaiscTypeListXml();
        $str = Serializer::serializeXml($obj);
        $this->assertStringContainsString('<BasicTypeList/>', $str);

        // all
        $obj = new BaiscTypeListXml(
            strValues: ['str1', 'str2'],
            intValues: [1234, 2234],
            boolValues: [true, true, false],
            floatValues: [3.14],
        );
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(8, $xml->count());
        $this->assertEquals(2, count($xml->StrValue));
        $this->assertEquals('str1', $xml->StrValue[0]);
        $this->assertEquals('str2', $xml->StrValue[1]);
        $this->assertEquals(2, count($xml->IntValue));
        $this->assertEquals('1234', $xml->IntValue[0]);
        $this->assertEquals('2234', $xml->IntValue[1]);
        $this->assertEquals(3, count($xml->BoolValue));
        $this->assertEquals('true', $xml->BoolValue[0]);
        $this->assertEquals('true', $xml->BoolValue[1]);
        $this->assertEquals('false', $xml->BoolValue[2]);
        $this->assertEquals(1, count($xml->FloatValue));
        $this->assertEquals('3.14', $xml->FloatValue[0]);
    }

    public function testSerializeMixedTypeXml()
    {
        // empty
        $obj = new MixedTypeXml();
        $str = Serializer::serializeXml($obj);
        $this->assertStringContainsString('<MixedType/>', $str);

        // all
        $obj = new MixedTypeXml(
            strValue: 'str-111',
            intValue: 12,
            xmlValue: new BaiscTypeXml(
                strValue: 'str',
                intValue: 123,
                boolValue: true,
                floatValue: 1.14,
            ),
            xmlListValue: new BaiscTypeListXml(
                strValues: ['str1', 'str2'],
                intValues: [1234, 2234],
                boolValues: [true, true, false],
                floatValues: [3.14],
            ),
        );

        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(4, $xml->count());
        $this->assertEquals('str-111', $xml->StrValue);
        $this->assertEquals('12', $xml->IntValue);

        $this->assertEquals(1, ($xml->BasicTypeFiled)->count());
        $this->assertEquals('str', $xml->BasicTypeFiled->StrValue);
        $this->assertEquals('123', $xml->BasicTypeFiled->IntValue);
        $this->assertEquals('true', $xml->BasicTypeFiled->BoolValue);
        $this->assertEquals('1.14', $xml->BasicTypeFiled->FloatValue);

        $this->assertEquals(1, ($xml->BasicTypeListFiled)->count());
        $this->assertEquals(2, ($xml->BasicTypeListFiled->StrValue)->count());
        $this->assertEquals('str1', $xml->BasicTypeListFiled->StrValue[0]);
        $this->assertEquals('str2', $xml->BasicTypeListFiled->StrValue[1]);
        $this->assertEquals(2, ($xml->BasicTypeListFiled->IntValue)->count());
        $this->assertEquals('1234', $xml->BasicTypeListFiled->IntValue[0]);
        $this->assertEquals('2234', $xml->BasicTypeListFiled->IntValue[1]);
        $this->assertEquals(3, ($xml->BasicTypeListFiled->BoolValue)->count());
        $this->assertEquals('true', $xml->BasicTypeListFiled->BoolValue[0]);
        $this->assertEquals('true', $xml->BasicTypeListFiled->BoolValue[1]);
        $this->assertEquals('false', $xml->BasicTypeListFiled->BoolValue[2]);
        $this->assertEquals(1, ($xml->BasicTypeListFiled->FloatValue)->count());
        $this->assertEquals('3.14', $xml->BasicTypeListFiled->FloatValue[0]);
    }

    public function testSerializeMixedTypeListXml()
    {
        // empty
        $obj = new MixedTypeListXml();
        $str = Serializer::serializeXml($obj);
        $this->assertStringContainsString('<MixedTypeList/>', $str);

        // all
        $obj = new MixedTypeListXml(
            strValue: 'str-111',
            intValue: 12,
            xmlValues: [
                new BaiscTypeXml(
                    strValue: 'str',
                    intValue: 123,
                    boolValue: true,
                    floatValue: 1.14,
                ),
                new BaiscTypeXml(
                    strValue: 'str-1',
                    intValue: 223,
                    boolValue: false,
                ),
            ],
        );

        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(4, $xml->count());
        $this->assertEquals('str-111', $xml->StrValue);
        $this->assertEquals('12', $xml->IntValue);

        $this->assertEquals(2, ($xml->BasicTypeFiled)->count());
        $this->assertEquals('str', $xml->BasicTypeFiled[0]->StrValue);
        $this->assertEquals('123', $xml->BasicTypeFiled[0]->IntValue);
        $this->assertEquals('true', $xml->BasicTypeFiled[0]->BoolValue);
        $this->assertEquals('1.14', $xml->BasicTypeFiled[0]->FloatValue);

        $this->assertEquals('str-1', $xml->BasicTypeFiled[1]->StrValue);
        $this->assertEquals('223', $xml->BasicTypeFiled[1]->IntValue);
        $this->assertEquals('false', $xml->BasicTypeFiled[1]->BoolValue);
        $this->assertFalse(isset($xml->BasicTypeFiled[1]->FloatValue));
    }

    public function testSerializeWithSpecialRootName()
    {
        $obj = new BaiscTypeXml();
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<BasicType/>', $str);

        // empty 
        $obj = new BaiscTypeXml();
        $str = Serializer::serializeXml($obj, 'MyRoot');
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<MyRoot/>', $str);
    }

    public function testSerializeLackXmlAnnotation()
    {
        //xml root
        $obj = new BaiscTypeLackAnnotationXml();
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<BaiscTypeLackAnnotationXml/>', $str);

        $obj = new BaiscTypeLackAnnotationXml();
        $str = Serializer::serializeXml($obj, 'MyRoot');
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<MyRoot/>', $str);

        //xml element
        $obj = new BaiscTypeLackAnnotationXml(
            strValue: 'str-123',
            intValue: 1234,
            boolValue: true,
            floatValue: 3.14,
        );
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(2, $xml->count());
        $this->assertEquals('str-123', $xml->StrValue);
        $this->assertEquals('1234', $xml->IntValue);
        $this->assertFalse(isset($xml->BoolValue));
        $this->assertFalse(isset($xml->FloatValue));
    }

    public function testSerializeDateTimeTypeXml()
    {
        //xml root
        $obj = new DatetimeTypeXml();
        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('<DatetimeType/>', $str);

        $date = new \DateTime();
        $date->setTimestamp(1702783809);

        $date1 = new \DateTimeImmutable();
        $date1 = $date1->setTimestamp(1702783819);

        //xml element
        $obj = new DatetimeTypeXml(
            isotimeValue: $date,
            dateTimeImmutableValue: $date1,
            httptimeValue: $date,
            unixtimeValue: $date,
        );

        $str = Serializer::serializeXml($obj);
        $this->assertNotEmpty($str);
        $xml = \simplexml_load_string($str);
        $this->assertEquals(4, $xml->count());
        $this->assertEquals('2023-12-17T03:30:09Z', $xml->DateTimeValue);
        $this->assertEquals('2023-12-17T03:30:19Z', $xml->DateTimeImmutableValue);
        $this->assertEquals('1702783809', $xml->UnixtimeValue);
        $this->assertEquals('Sun, 17 Dec 2023 03:30:09 GMT', $xml->HttptimeValue);
    }
}
