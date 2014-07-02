<?php
namespace Exar\Reflection;

use Exar\Annotation\AnnotationParser;

class AnnotationContainer implements ReflectionInterface {
	private $annotations = array();

	public function __construct(\Reflector $reflectionObject) {
		$comment = $reflectionObject->getDocComment();
		
		if ($comment != '') {
			$this->annotations = AnnotationParser::getInstance()->readAnnotations($comment, $reflectionObject);
		}
	}

	public function hasAnnotation($annotation) {
		return isset($this->annotations[$annotation]);
	}

	public function getAnnotation($annotation) {
		return ($this->hasAnnotation($annotation)) ? $this->annotations[$annotation][0] : null;
	}

	public function getAnnotationMap() {
		$arr = array();
		foreach($this->annotations as $name => $annotationArr) {
			$arr[] = $annotationArr[0];
		}
		return $arr;
	}
	
	public function getAnnotations($considerMultipleFlag = false) {
		$arr = array();
		foreach($this->annotations as $name => $annotationArr) {
			if ($considerMultipleFlag) { // Non-multiple annotations will be returned only once
				$annotation = $annotationArr[0];
				$classReflection = new ReflectionClass(get_class($annotation));
				if ($classReflection->hasAnnotation('Multiple')) { // Multiple annotations are allowed
					$arr = array_merge($arr, $annotationArr);
				} else {
					$arr[] = $annotation;
				}
			} else {
				$arr = array_merge($arr, $annotationArr);
			}
		}
		return $arr;
	}

}