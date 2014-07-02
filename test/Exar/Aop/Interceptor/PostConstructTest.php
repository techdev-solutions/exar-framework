<?php
namespace Exar\Aop\Interceptor;

use Exar\ExarTest;
use Exar\Reflection\ReflectionClass;
use Exar\TestClasses\ClassWithConstructorParams;
use Exar\TestClasses\ClassWithoutConstructor;
use Exar\TestClasses\ClassWithParentConstructor;
use Exar\TestClasses\ClassWithParentsParentConstructor;
use Exar\TestClasses\ClassWithPostConstruct;

class PostConstructTest extends ExarTest {

    public function testPostConstruct() {
        $obj = new ClassWithPostConstruct();

        // the value is set within the PostConstruct annotated method
        $this->assertEquals(123, $obj->getValue());
    }

    public function testWithoutConstructor() {
        $obj = new ClassWithoutConstructor();

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithoutConstructor');
        $this->assertTrue($class->hasMethod('__construct'));

        // the value is set within the PostConstruct annotated method
        $this->assertEquals(345, $obj->getValue());
    }

    public function testWithConstructorParams() {
        $obj = new ClassWithConstructorParams('foo', 'bar');

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithoutConstructor');
        $this->assertTrue($class->hasMethod('__construct'));

        $this->assertEquals('foo', $obj->getParam1());
        $this->assertEquals('bar', $obj->getParam2());

        // the value is set within the PostConstruct annotated method
        $this->assertEquals(345, $obj->getValue());
    }

    public function testWithParentConstructor() {
        $obj = new ClassWithParentConstructor('foo', 'bar');

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithoutConstructor');
        $this->assertTrue($class->hasMethod('__construct'));

        $this->assertEquals('foo', $obj->getParam1());
        $this->assertEquals('bar', $obj->getParam2());

        // the value is set within the PostConstruct annotated method of the parent class
        $this->assertEquals(345, $obj->getValue());

        // the value is set within the PostConstruct annotated method
        $this->assertEquals(789, $obj->getAnotherValue());
    }

    public function testWithParentsParentConstructor() {
        $obj = new ClassWithParentsParentConstructor('bar', 'baz');

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithoutConstructor');
        $this->assertTrue($class->hasMethod('__construct'));

        $this->assertEquals('bar', $obj->getParam1());
        $this->assertEquals('baz', $obj->getParam2());

        // the value is set within the PostConstruct annotated method of the parent's parent class
        $this->assertEquals(345, $obj->getValue());

        // the value is set within the PostConstruct annotated method
        $this->assertEquals(654, $obj->getAnotherValue());
    }

}
