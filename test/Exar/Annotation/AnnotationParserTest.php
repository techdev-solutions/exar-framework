<?php
namespace Exar\Annotation;

use Exar\ExarTest;
use Exar\Reflection\ReflectionClass;

class AnnotationParserTest extends ExarTest {
    public function testAnnotations() {
        $parser = AnnotationParser::getInstance();

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithComments');
        $annotations = $parser->readAnnotations($class->getDocComment(), $class);

        $this->assertEquals(3, count($annotations));
        $this->assertEquals(array('Exar', 'CorrectAnnotation', 'AnotherCorrectAnnotation'), array_keys($annotations));
    }
}
 