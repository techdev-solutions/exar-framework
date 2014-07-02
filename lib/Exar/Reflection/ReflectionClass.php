<?php
namespace Exar\Reflection;

class ReflectionClass extends \ReflectionClass implements ReflectionInterface {
	private $annotationContainer;

	public function __construct($class) {
		parent::__construct($class);
        $this->annotationContainer = new AnnotationContainer($this);
	}

	public function hasAnnotation($annotation) {
		return $this->annotationContainer->hasAnnotation($annotation);
	}

	public function getAnnotation($annotation) {
		return $this->annotationContainer->getAnnotation($annotation);
	}

	public function getAnnotationMap() {
		return $this->annotationContainer->getAnnotationMap();
	}

	public function getAnnotations($considerMultipleFlag = false) {
		return $this->annotationContainer->getAnnotations($considerMultipleFlag);
	}

	public function getMethod($name) {
		return new ReflectionMethod($this->getName(), $name);
	}

	public function getMethods($filter = -1) {
		$arr = array();
		foreach(parent::getMethods($filter) as $method) {
			$arr[] = new ReflectionMethod($this->getName(), $method->getName());
		}
		return $arr;
	}
	
    public function getProperty($name) {
    	return new ReflectionProperty($this->getName(), $name);
    }

    public function getProperties($filter = -1) {
    	$arr = array();
		foreach(parent::getProperties($filter) as $property) {
			if ($property->getDeclaringClass()->getName() == $this->getName()) {
				$arr[] = new ReflectionProperty($this->getName(), $property->getName());
			}
		}
		return $arr;
    }
}