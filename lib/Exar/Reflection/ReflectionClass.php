<?php
namespace Exar\Reflection;

/**
 * The ReflectionClass class reports information about a class and provides the extended Reflection API.
 */
class ReflectionClass extends \ReflectionClass implements ReflectionInterface {
	private $annotationContainer;

	/**
	 * Constructor.
	 *
	 * @see \ReflectionClass::__construct()
	 */
	public function __construct($class) {
		parent::__construct($class);
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
	public function getAnnotations($considerMultipleFlag = false) {
		return $this->annotationContainer->getAnnotations($considerMultipleFlag);
	}

	/**
	 * Returns a ReflectionMethod object for a class method.
	 *
	 * @param string $name the method name to reflect
	 * @return ReflectionMethod a ReflectionMethod object providing the extended Reflection API
	 */
	public function getMethod($name) {
		return new ReflectionMethod($this->getName(), $name);
	}

	/**
	 * Gets an array of methods.
	 *
	 * @param int $filter filter the results to include only methods with certain attributes
	 * @return array array of ReflectionMethod objects
	 */
	public function getMethods($filter = -1) {
		$arr = array();
		foreach(parent::getMethods($filter) as $method) {
			$arr[] = new ReflectionMethod($this->getName(), $method->getName());
		}
		return $arr;
	}

	/**
	 * Returns a ReflectionProperty for a class's property.
	 *
	 * @param string $name the property name to reflect
	 * @return ReflectionProperty a ReflectionProperty object providing the extended Reflection API
	 */
    public function getProperty($name) {
    	return new ReflectionProperty($this->getName(), $name);
    }

	/**
	 * Gets an array of properties.
	 *
	 * @param int $filter filter the results to include only methods with certain attributes
	 * @return array array of ReflectionProperty objects
	 */
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