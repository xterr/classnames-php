<?php

namespace xterrunit\ClassNames;

use PHPUnit\Framework\TestCase;
use function xterr\ClassNames\classNames;
use xterr\ClassNames\ClassNames;

class ClassNamesTest extends TestCase
{
    public function testStringParams()
    {
        $sClass = 'test-class';
        $actual = classNames($sClass);
        $this->assertEquals($sClass, $actual);
    }

    public function testArrayOfStringsParams()
    {
        $aClasses = ['test-class', 'test-class2', 'test-class3'];
        $actual   = classNames($aClasses);
        $this->assertEquals(implode(' ', $aClasses), $actual);
    }

    public function testCallableParams()
    {
        $actual   = classNames(function() {
            return 'test-class';
        });
        $this->assertEquals('test-class', $actual);

        $actual   = classNames(function() {
            return ['test-class', 'test-class2'];
        });
        $this->assertEquals('test-class test-class2', $actual);

        $actual = classNames([ExampleObject::class, 'getClasses']);
        $this->assertEquals('static-callable', $actual);

        $actual = classNames(['class1'], 'class2', [ExampleObject::class, 'getClasses']);
        $this->assertEquals('class1 class2 static-callable', $actual);

        $actual = classNames(['static-callable' => FALSE], [ExampleObject::class, 'getClasses']);
        $this->assertEquals('static-callable', $actual);

        $oObj = new ExampleObject('');

        $actual = classNames(['class1'], 'class2', [$oObj, 'getClassesDynamic']);
        $this->assertEquals('class1 class2 dynamic-callable', $actual);

        $actual = classNames(['dynamic-callable' => FALSE], [$oObj, 'getClassesDynamic']);
        $this->assertEquals('dynamic-callable', $actual);
    }

    public function testObjectParams()
    {
        $example = new ExampleObject('object-test');
        $actual   = classNames($example);
        $this->assertEquals('object-test', $actual);
    }

    public function testConditionalArrayParam()
    {
        $aClasses = [
            'test-class',
            'test-condition-class'       => TRUE,
            'test-false-condition-class' => FALSE,
        ];
        $actual = classNames($aClasses);
        $expected = 'test-class test-condition-class';
        $this->assertEquals($expected, $actual);
    }

    public function testConditionalOverride()
    {
        $aClasses = [
            'test-class',
            'test-condition-class'       => TRUE,
            'test-false-condition-class' => FALSE,
            'override'                   => TRUE,
        ];

        $actual = classNames($aClasses, ['override' => FALSE]);
        $expected = 'test-class test-condition-class';
        $this->assertEquals($expected, $actual);
    }

    public function testArrayOfArrays()
    {
        $actual = classNames([[['override' => TRUE, 'override-condition']]], ['override2' => TRUE, 'override-condition' => FALSE]);
        $expected = 'override override2';
        $this->assertEquals($expected, $actual);
    }

    public function testWithIntegerOrFloatAsKeys()
    {
        $actual = classNames(1.5);
        $expected = '1.5';
        $this->assertEquals($expected, $actual);

        $actual = classNames(['1.5' => TRUE]);
        $expected = '1.5';
        $this->assertEquals($expected, $actual);

        $actual = classNames(['1' => TRUE]);
        $expected = '1';
        $this->assertEquals($expected, $actual);
    }

    public function testDifferentTypesParams()
    {
        $example = new ExampleObject('object-test');
        $actual = classNames('test1', ['test2' => TRUE, 'test3' => FALSE], 'test4', $example, function() {return 'callable-test';});
        $expected = 'test1 test2 test4 object-test callable-test';
        $this->assertEquals($expected, $actual);
    }

    public function testInvalidValues()
    {
        $example  = new ExampleObject('object-test');
        $actual   = classNames('null', null, 0, false, 1.5, new \stdClass(), function () {}, $example);
        $expected = 'null 1.5 object-test';
        $this->assertEquals($expected, $actual);

        $actual   = classNames('null', [TRUE, FALSE]);
        $expected = 'null';
        $this->assertEquals($expected, $actual);
    }

    public function testNoArgs()
    {
        $actual = classNames();
        $expected = '';
        $this->assertSame($expected, $actual);
    }

    public function testWithInstance()
    {
        $oInstance = new ClassNames();
        $this->assertEquals('class1', $oInstance('class1'));
    }
}

class ExampleObject
{
    /**
     * @var string
     */
    protected $stringVal;

    public function __construct($value)
    {
        $this->stringVal = $value;
    }

    public function __toString()
    {
        return $this->stringVal;
    }

    public static function getClasses()
    {
        return 'static-callable';
    }

    public function getClassesDynamic()
    {
        return 'dynamic-callable';
    }
}
