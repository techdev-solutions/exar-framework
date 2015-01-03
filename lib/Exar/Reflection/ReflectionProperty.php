<?php
namespace Exar\Reflection;

class ReflectionProperty extends \ReflectionProperty implements ReflectionInterface {
	private $annotationContainer;

	public function __construct($class, $name) {
		parent::__construct($class, $name);
        $this->annotationContainer = new AnnotationContainer($this);
	}

    public function hasAnnotation($name) {
        return $this->annotationContainer->hasAnnotation($name);
    }

    public function getAnnotation($name) {
        return $this->annotationContainer->getAnnotation($name);
    }

    public function getAnnotationMap() {
        return $this->annotationContainer->getAnnotationMap();
    }

    public function getAnnotations($considerMultipleFlag = false) {
        return $this->annotationContainer->getAnnotations($considerMultipleFlag);
    }
}
