<?php

namespace UnitTests\Types;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeListXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'MixedTypeXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'MixedTypeListXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'BaiscTypeLackAnnotationXml.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'DatetimeTypeXml.php';

use AlibabaCloud\Oss\V2\OperationOutput;
use AlibabaCloud\Oss\V2\Deserializer;
use AlibabaCloud\Oss\V2\Utils;
use AlibabaCloud\Oss\V2\Exception\DeserializationExecption;

use UnitTests\Fixtures\BaiscTypeXml;
use UnitTests\Fixtures\BaiscTypeListXml;
use UnitTests\Fixtures\MixedTypeXml;
use UnitTests\Fixtures\MixedTypeListXml;
use UnitTests\Fixtures\BaiscTypeLackAnnotationXml;
use UnitTests\Fixtures\DatetimeTypeXml;


class DeserializerTest extends \PHPUnit\Framework\TestCase
{
    public function testDeserializeBaiscTypeXml()
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <BasicType>
                <StrValue>id-124</StrValue>
                <IntValue>1234</IntValue>
                <BoolValue>true</BoolValue>
                <FloatValue>3.14</FloatValue>
            </BasicType>';

        $xml = new BaiscTypeXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertEquals('id-124', $xml->strValue);
        $this->assertEquals(1234, $xml->intValue);
        $this->assertEquals(true, $xml->boolValue);
        $this->assertEquals(3.14, $xml->floatValue);


        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <BasicType>
            </BasicType>';

        $xml = new BaiscTypeXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertFalse(isset($xml->StrValue));
        $this->assertFalse(isset($xml->IntValue));
        $this->assertFalse(isset($xml->BoolValue));
        $this->assertFalse(isset($xml->FloatValue));

        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <BasicType>
                <IntValue>1234</IntValue>
                <BoolValue>true</BoolValue>            
            </BasicType>';

        $xml = new BaiscTypeXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertFalse(isset($xml->StrValue));
        $this->assertEquals(1234, $xml->intValue);
        $this->assertEquals(true, $xml->boolValue);
        $this->assertFalse(isset($xml->FloatValue));
    }

    public function testDeserializeBaiscTypeListXml(): void
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <BasicTypeList>
                <StrValue>str1</StrValue>
                <StrValue>str2</StrValue>
                <IntValue>1234</IntValue>
                <IntValue>2234</IntValue>
                <BoolValue>true</BoolValue>
                <BoolValue>true</BoolValue>
                <BoolValue>false</BoolValue>
                <FloatValue>3.14</FloatValue>      
            </BasicTypeList>';
        $xml = new BaiscTypeListXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertEquals(2, count($xml->strValues));
        $this->assertEquals('str1', $xml->strValues[0]);
        $this->assertEquals('str2', $xml->strValues[1]);
        $this->assertEquals(2, count($xml->intValues));
        $this->assertEquals(1234, $xml->intValues[0]);
        $this->assertEquals(2234, $xml->intValues[1]);
        $this->assertEquals(3, count($xml->boolValues));
        $this->assertEquals(true, $xml->boolValues[0]);
        $this->assertEquals(true, $xml->boolValues[1]);
        $this->assertEquals(false, $xml->boolValues[2]);
        $this->assertEquals(1, count($xml->floatValues));
        $this->assertEquals('3.14', $xml->floatValues[0]);

        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <BasicTypeList>
            </BasicTypeList>';

        $xml = new BaiscTypeListXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertFalse(isset($xml->strValues));
        $this->assertFalse(isset($xml->intValues));
        $this->assertFalse(isset($xml->boolValues));
        $this->assertFalse(isset($xml->floatValues));
    }

    public function testDeserializeDateTimeTypeXml()
    {
        $date = new \DateTime();
        $date->setTimestamp(1702783809);
        $date1 = new \DateTimeImmutable();
        $date1 = $date1->setTimestamp(1702783819);

        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <DatetimeType>
                <DateTimeValue>2023-12-17T03:30:09Z</DateTimeValue>
                <UnixtimeValue>1702783809</UnixtimeValue>
                <HttptimeValue>Sun, 17 Dec 2023 03:30:09 GMT</HttptimeValue>
            </DatetimeType>';
        $xml = new DatetimeTypeXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertEquals($date, $xml->isotimeValue);
        $this->assertFalse(isset($xml->dateTimeImmutableValue));
        $this->assertEquals($date, $xml->unixtimeValue);
        $this->assertEquals($date, $xml->httptimeValue);
    }

    public function testDeserializeMixedTypeXml(): void
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <MixedType>
                <StrValue>str-111</StrValue>
                <IntValue>12</IntValue>
                <BasicTypeFiled>
                    <StrValue>str</StrValue>
                    <IntValue>123</IntValue>
                    <BoolValue>true</BoolValue>
                    <FloatValue>1.14</FloatValue>
                </BasicTypeFiled>
                <BasicTypeListFiled>
                    <StrValue>str1</StrValue>
                    <StrValue>str2</StrValue>
                    <IntValue>1234</IntValue>
                    <IntValue>2234</IntValue>
                    <BoolValue>true</BoolValue>
                    <BoolValue>true</BoolValue>
                    <BoolValue>false</BoolValue>
                    <FloatValue>3.14</FloatValue>    
                </BasicTypeListFiled>
            </MixedType>';

        $xml = new MixedTypeXml();
        Deserializer::deserializeXml($str, $xml);
        $this->assertEquals('str-111', $xml->strValue);
        $this->assertEquals(12, $xml->intValue);

        $this->assertEquals('str', $xml->xmlValue->strValue);
        $this->assertEquals(123, $xml->xmlValue->intValue);
        $this->assertEquals(true, $xml->xmlValue->boolValue);
        $this->assertEquals(1.14, $xml->xmlValue->floatValue);

        $this->assertEquals(2, count($xml->xmlListValue->strValues));
        $this->assertEquals('str1', $xml->xmlListValue->strValues[0]);
        $this->assertEquals('str2', $xml->xmlListValue->strValues[1]);
        $this->assertEquals(2, count($xml->xmlListValue->intValues));
        $this->assertEquals(1234, $xml->xmlListValue->intValues[0]);
        $this->assertEquals(2234, $xml->xmlListValue->intValues[1]);
        $this->assertEquals(3, count($xml->xmlListValue->boolValues));
        $this->assertEquals(true, $xml->xmlListValue->boolValues[0]);
        $this->assertEquals(true, $xml->xmlListValue->boolValues[1]);
        $this->assertEquals(false, $xml->xmlListValue->boolValues[2]);
        $this->assertEquals(1, count($xml->xmlListValue->floatValues));
        $this->assertEquals('3.14', $xml->xmlListValue->floatValues[0]);
    }

    public function testDeserializeMixedTypeListXml(): void
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <MixedTypeList>
                <StrValue>str-111</StrValue>
                <IntValue>12</IntValue>
                <BasicTypeFiled>
                    <StrValue>str</StrValue>
                    <IntValue>123</IntValue>
                    <BoolValue>true</BoolValue>
                    <FloatValue>1.14</FloatValue>
                </BasicTypeFiled>
                <BasicTypeFiled>
                    <StrValue>str-1</StrValue>
                    <IntValue>223</IntValue>
                    <BoolValue>false</BoolValue>
                </BasicTypeFiled>
            </MixedTypeList>';

        $xml = new MixedTypeListXml();
        Deserializer::deserializeXml($str, $xml);

        $this->assertEquals('str-111', $xml->strValue);
        $this->assertEquals(12, $xml->intValue);

        $this->assertEquals(2, count($xml->xmlValues));
        $this->assertEquals('str', $xml->xmlValues[0]->strValue);
        $this->assertEquals(123, $xml->xmlValues[0]->intValue);
        $this->assertEquals(true, $xml->xmlValues[0]->boolValue);
        $this->assertEquals(1.14, $xml->xmlValues[0]->floatValue);

        $this->assertEquals('str-1', $xml->xmlValues[1]->strValue);
        $this->assertEquals(223, $xml->xmlValues[1]->intValue);
        $this->assertEquals(false, $xml->xmlValues[1]->boolValue);
        $this->assertFalse(isset($xml->xmlValues[1]->floatValue));
    }

    public function testDeserializeCheckXmlRoot(): void
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>
            <Root>
                <StrValue>str-111</StrValue>
                <IntValue>12</IntValue>
                <BasicTypeFiled>
                    <StrValue>str</StrValue>
                    <IntValue>123</IntValue>
                    <BoolValue>true</BoolValue>
                    <FloatValue>1.14</FloatValue>
                </BasicTypeFiled>
                <BasicTypeFiled>
                    <StrValue>str-1</StrValue>
                    <IntValue>223</IntValue>
                    <BoolValue>false</BoolValue>
                </BasicTypeFiled>
            </Root>';

        $xml = new MixedTypeListXml();

        try {
            Deserializer::deserializeXml($str, $xml, 'MixedTypeList');
            $this->assertTrue(false, "shoud not here");
        } catch (DeserializationExecption $e) {
            $this->assertStringContainsString("Deserialization raised an exception: Not found tag <MixedTypeList>", (string)$e);
        } catch (\Exception $e) {
            $this->assertTrue(false, "shoud not here");
        }
    }

    public function testDeserializeInvalidXml(): void
    {
        $str = 'invalid xml';

        $xml = new MixedTypeListXml();

        try {
            Deserializer::deserializeXml($str, $xml, 'MixedTypeList');
            $this->assertTrue(false, "shoud not here");
        } catch (DeserializationExecption $e) {
            $this->assertStringContainsString("simplexml_load_string returns false", (string)$e);
        } catch (\Exception $e) {
            $this->assertTrue(false, "shoud not here");
        }
    }
}
