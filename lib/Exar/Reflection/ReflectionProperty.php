<?php
namespace Exar\Reflection;

/**
 * The ReflectionProperty class reports information about class properties and provides the extended Reflection API.
 */
class ReflectionProperty extends \ReflectionProperty implements ReflectionInterface {
	private $annotationContainer;

    /**
     * Constructor.
     *
     * @see \ReflectionProperty::__construct()
     */
	public function __construct($class, $name) {
		parent::__construct($class, $name);
        $this->annotationContainer = new AnnotationContainer($this);
	}

    /**
     * @see Exar\Reflection\ReflectionInterface::hasAnnotation()
     */
    public function hasAnnotation($name) {
        return $this->annotationContainer->hasAnnotation($name);
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotation()
     */
    public function getAnnotation($name) {
        return $this->annotationContainer->getAnnotation($name);
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotationMap()
     */
    public function getAnnotationMap() {
        return $this->annotationContainer->getAnnotationMap();
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotations()
     */
    public function getAnnotations($considerMultipleTag = false) {
        return $this->annotationContainer->getAnnotations($considerMultipleTag);
    }
}
