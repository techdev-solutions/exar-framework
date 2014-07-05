<?php
namespace Exar\Annotation;

use Exar\Annotation\Matcher\AnnotationsMatcher;

class AnnotationParser {
	static private $instance = null;
	
	const EXAR_ANNOTATION_NAMESPACE = '\\Exar\\Aop\\Interceptor';
	
	static private $namespaces;
	static private $matcher;
	
	static public function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	private function __construct() {
		self::$namespaces = array();
		self::$namespaces[] = self::EXAR_ANNOTATION_NAMESPACE;
		self::$matcher = new AnnotationsMatcher();
	}
	
	public function readAnnotations($docBlock, $targetReflection) {
		$lines = explode(PHP_EOL, trim($docBlock));

		$annotations = array();
		foreach ($lines as $line) {
			if(!preg_match('/@[A-Z]/', $line, $matches, PREG_OFFSET_CAPTURE)) {
				continue; // ignore lines which do not start with an annotation
			}
			$line = substr($line, $matches[0][1] + 1);
			$annotationName = trim($line);
			
			$arr = self::$matcher->match($line);

			if ($arr['name'] !== null) {
				$annotationName = $arr['name'];
			} else {
				$arr['name'] = $annotationName;
			}
			
			$parameters = $arr['parameters']; // annotation parameters
			
			if (!isset($annotations[$annotationName])) {
				$annotations[$annotationName] = array(); // initialize annotation array (every target can contain several annotations with the same name)
			}
			
			$annotationInstantiated = false;
			foreach (self::$namespaces as $namespace) { // walk through registered annotaion namespaces
				try {
					$className = $namespace.'\\'.$annotationName;
					
					if (!in_array($className, get_declared_classes())) { // class is not declared yet
						if (!\Exar\Autoloader::autoload($className)) { // class definition not found
							continue;
						}
					}
					
					$rAnnotation = new \ReflectionClass($className);
					$annotation = $rAnnotation->newInstance($parameters, $targetReflection);
					array_unshift($annotations[$annotationName], $annotation);
					$annotationInstantiated = true;
					break;
				} catch (\ReflectionException $e) {
					// There was no annotation found within the current namespace
				}
			}
			
			if (!$annotationInstantiated) {
				$simpleAnnotation = new SimpleAnnotation($parameters, $targetReflection, $annotationName);
				array_unshift($annotations[$annotationName], $simpleAnnotation);
			}
		}
		return $annotations;
	}
}