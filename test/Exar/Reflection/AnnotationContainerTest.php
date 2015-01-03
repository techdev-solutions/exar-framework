<?php
namespace Exar\Reflection;

use Exar\Autoloader;
use Exar\ExarTest;
use Exar\TestClasses\ClassAnnotatedClassForAnnotationContainer;

class AnnotationContainerTest extends ExarTest {
	private static $container;

	public static function setUpBeforeClass() {
		Autoloader::addAnnotationNamespaces('\Exar\TestClasses\Annotation');
		self::$container = new AnnotationContainer(new \ReflectionClass(ClassAnnotatedClassForAnnotationContainer::CLASSNAME));
	}

	public function testHasAnnotation() {
		$this->assertTrue(self::$container->hasAnnotation('One'));
		$this->assertTrue(self::$container->hasAnnotation('Two'));
		$this->assertTrue(self::$container->hasAnnotation('Three'));
		$this->assertTrue(self::$container->hasAnnotation('MultipleAnnotation'));
		$this->assertFalse(self::$container->hasAnnotation('Four'));
	}

	public function testGetAnnotation() {
		$this->assertEquals('i', self::$container->getAnnotation('One')->getValue());
		$this->assertEquals('g', self::$container->getAnnotation('Two')->getValue());
		$this->assertEquals('c', self::$container->getAnnotation('Three')->getValue());
		$this->assertEquals('h', self::$container->getAnnotation('MultipleAnnotation')->getValue());
		$this->assertNull(self::$container->getAnnotation('Four'));
	}

	public function testGetAnnotationsWithMultipleTag() {
		$annotations = self::$container->getAnnotations(true);

		$this->assertEquals(6, count($annotations));

		$this->assertEquals('i', $annotations[0]->getValue());
		$this->assertEquals('g', $annotations[1]->getValue());
		$this->assertEquals('c', $annotations[2]->getValue());
		$this->assertEquals('h', $annotations[3]->getValue());
		$this->assertEquals('f', $annotations[4]->getValue());
		$this->assertEquals('d', $annotations[5]->getValue());
	}

	public function testGetAnnotationsWithoutMultipleTag() {
		$annotations = self::$container->getAnnotations(false);

		$this->assertEquals(9, count($annotations));

		$this->assertEquals('i', $annotations[0]->getValue());
		$this->assertEquals('e', $annotations[1]->getValue());
		$this->assertEquals('a', $annotations[2]->getValue());
		$this->assertEquals('g', $annotations[3]->getValue());
		$this->assertEquals('b', $annotations[4]->getValue());
		$this->assertEquals('c', $annotations[5]->getValue());
		$this->assertEquals('h', $annotations[6]->getValue());
		$this->assertEquals('f', $annotations[7]->getValue());
		$this->assertEquals('d', $annotations[8]->getValue());
	}

	public function testGetAnnotationMap() {
		$arr = self::$container->getAnnotationMap(false);

		$this->assertEquals(4, count($arr));

		$this->assertTrue(array_key_exists('One', $arr));
		$this->assertTrue(array_key_exists('Two', $arr));
		$this->assertTrue(array_key_exists('Three', $arr));
		$this->assertTrue(array_key_exists('MultipleAnnotation', $arr));
		$this->assertFalse(array_key_exists('Four', $arr));

		$this->assertEquals(3, count($arr['One']));
		$this->assertEquals(2, count($arr['Two']));
		$this->assertEquals(1, count($arr['Three']));
		$this->assertEquals(3, count($arr['MultipleAnnotation']));
	}
	
}
