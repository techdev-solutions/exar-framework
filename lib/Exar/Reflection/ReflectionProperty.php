<?php
namespace Exar\Reflection;

class ReflectionProperty extends \ReflectionProperty implements ReflectionInterface {
	private $annotationContainer;

	public function __construct($class, $name) {
		parent::__construct($class, $name);
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
}
