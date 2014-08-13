<?php
namespace Exar\Aop\Interceptor;

use Exar\ExarTest;
use Exar\Reflection\ReflectionClass;

class MyAnnotationTest extends ExarTest {

    public function testMyAnnotation() {
        $class = new ReflectionClass('\Exar\TestClasses\MyAnnotationAnnotatedClass');
        $this->assertTrue($class->hasAnnotation('MyAnnotation'));
        var_dump($class->hasAnnotation('MyAnnotation'));
        var_dump($class->getAnnotation('MyAnnotation'));
        //$this->assertTrue(get_class($class->getAnnotation('MyAnnotation')) == 'Exar\\Aop\\MyAnnotation');
    }

}
