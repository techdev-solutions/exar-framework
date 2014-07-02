<?php
namespace Exar\Reflection;

use Exar\SimpleTest;

class ReflectionClassTest extends SimpleTest {

	public function testClassAnnotation() {
		$r = new \Exar\TestClasses\ClassAnnotatedClass();
		
		$class = new ReflectionClass('\Exar\TestClasses\ClassAnnotatedClass');
		
		$annotations = $class->getAnnotations();
		
		$this->assertEquals(6, count($annotations));
		
		$noValue = $annotations[0];
		$this->assertTrue($noValue instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('NoValue', $noValue->getName());
		$this->assertNull($noValue->getValue());
		
		$floatValue = $annotations[1];
		$this->assertTrue($floatValue instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('FloatValue', $floatValue->getName());
		$this->assertEquals(1.5, $floatValue->getValue());
		
		$stringValue = $annotations[2];
		$this->assertTrue($stringValue instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('StringValue', $stringValue->getName());
		$this->assertEquals('string value', $stringValue->getValue());
		
		$arrayValue = $annotations[3];
		$this->assertTrue($arrayValue instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('ArrayValue', $arrayValue->getName());
		$this->assertEquals(array(1, 2, 3), $arrayValue->getValue());
		
		$arrayValue2 = $annotations[4];
		$this->assertTrue($arrayValue2 instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('ArrayValue', $arrayValue2->getName());
		$this->assertEquals(array('a', 'b', 'c'), $arrayValue2->getValue());
		
		$withNamespace = $annotations[5];
		$this->assertTrue($withNamespace instanceof \Exar\Annotation\SimpleAnnotation);
		$this->assertEquals('Annotation\With\Namespace', $withNamespace->getName());
		$this->assertEquals(array('a', true, null), $withNamespace->getValue());
		
		$this->assertEquals($noValue, $class->getAnnotation('NoValue'));
		$this->assertEquals($arrayValue, $class->getAnnotation('ArrayValue'));
	}
	
}
