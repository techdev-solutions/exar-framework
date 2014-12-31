<?php
namespace Exar\Annotation;

use Exar\Autoloader;
use Exar\ExarTest;
use Exar\Reflection\ReflectionClass;
use Exar\TestClasses\Annotation\CustomAnnotation;

class AnnotationParserTest extends ExarTest {
    public function testIgnoreAnnotationsWithinComments() {
        $parser = AnnotationParser::getInstance();

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithComments');
        $annotations = $parser->readAnnotations($class->getDocComment(), $class);

        $this->assertEquals(3, count($annotations));
        $this->assertEquals(array('Exar', 'CorrectAnnotation', 'AnotherCorrectAnnotation'), array_keys($annotations));
    }

    public function testCustomAnnotations() {
        Autoloader::addAnnotationNamespaces('\Exar\TestClasses\Annotation');
        $parser = AnnotationParser::getInstance();

        $class = new ReflectionClass('\Exar\TestClasses\ClassWithCustomAnnotation');
        $annotations = $parser->readAnnotations($class->getDocComment(), $class);

        $exarAnnotation = $annotations['Exar'][0];
        $this->assertTrue($exarAnnotation instanceof SimpleAnnotation);

        $customAnnotation = $annotations['CustomAnnotation'][0];
        $this->assertTrue($customAnnotation instanceof CustomAnnotation);
    }
}
 