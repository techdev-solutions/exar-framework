<?php
namespace Exar\Annotation;

use Exar\Annotation\Matcher\AnnotationsMatcher;
use Exar\Autoloader;

class AnnotationParser {
    static private $instance = null;

    static private $matcher;

    static public function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        self::$matcher = new AnnotationsMatcher();
    }

    public function readAnnotations($docBlock, $targetReflection) {
        $lines = explode(PHP_EOL, trim($docBlock)); // get docblock lines

        $annotations = array();
        foreach ($lines as $line) {
            $line = preg_replace('/^\/\*\*/', '', trim($line));
            $line = trim(preg_replace('/^\*/', '', $line));
            if(!preg_match('/^@[A-Z]/', $line, $matches, PREG_OFFSET_CAPTURE)) {
                continue; // ignore lines which do not start with an annotation
            }
            $line = substr($line, $matches[0][1] + 1); // extract annotation name, ignore '@'
            $annotationName = trim($line);

            $arr = self::$matcher->match($line); // annotation data

            if ($arr['name'] !== null) {
                $annotationName = $arr['name'];
            } else {
                $arr['name'] = $annotationName; // annotation without parameters
            }

            $parameters = $arr['parameters']; // get annotation parameters

            if (!isset($annotations[$annotationName])) {
                $annotations[$annotationName] = array(); // initialize annotation array (every target can contain several annotations with the same name)
            }

            $annotationInstantiated = false; // initial value - annotation object is not instantiated yet

            foreach (Autoloader::getAnnotationNamespaces() as $namespace) { // walk through registered annotation namespaces
                try {
                    $className = $namespace.'\\'.$annotationName; // build class name for the annotation object

                    if (!in_array($className, get_declared_classes())) { // class is not declared yet
                        if (!\Exar\Autoloader::autoload($className)) { // class definition not found
                            continue; // do nothing, jump to the next registered annotation namespaces
                        }
                    }

                    $rAnnotation = new \ReflectionClass($className); // create reflection object of the annotation class
                    $annotation = $rAnnotation->newInstance($parameters, $targetReflection); // instantiate annotation object
                    array_unshift($annotations[$annotationName], $annotation); // remember created annotation object in an array
                    $annotationInstantiated = true; // set instantiation flag to "true"
                    break; // since the annotation object is created, we don't need to check other namespaces and can leave the loop
                } catch (\ReflectionException $e) {
                    // There was no annotation found within the current namespace
                }
            }

            if (!$annotationInstantiated) { // no annotation instantiated, so create an simple annotation object
                $simpleAnnotation = new SimpleAnnotation($parameters, $targetReflection, $annotationName);
                array_unshift($annotations[$annotationName], $simpleAnnotation);
            }
        }
        return $annotations;
    }
}