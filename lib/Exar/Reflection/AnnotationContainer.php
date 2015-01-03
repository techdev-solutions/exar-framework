<?php
namespace Exar\Reflection;

use Exar\Annotation\AnnotationParser;

/**
 * Container class which extends the standard Reflection API by extracting and storing information about annotations
 * of the specified element in an array.
 *
 * @see Exar\Reflection\ReflectionInterface
 */
class AnnotationContainer implements ReflectionInterface {
    private $annotations = array();

    /**
     * Constructor.
     *
     * @param \Reflector $reflectionObject reflection object to extend
     */
    public function __construct(\Reflector $reflectionObject) {
        $comment = $reflectionObject->getDocComment();

        if ($comment != '') {
            $this->annotations = AnnotationParser::getInstance()->readAnnotations($comment, $reflectionObject);
        }
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::hasAnnotation()
     */
    public function hasAnnotation($name) {
        return isset($this->annotations[$name]);
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotation()
     */
    public function getAnnotation($name) {
        return ($this->hasAnnotation($name)) ? $this->annotations[$name][0] : null;
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotationMap()
     */
    public function getAnnotationMap() {
        return $this->annotations;
    }

    /**
     * @see Exar\Reflection\ReflectionInterface::getAnnotations()
     */
    public function getAnnotations($considerMultipleTag = false) {
        $arr = array();
        foreach($this->annotations as $name => $annotationArr) {
            if ($considerMultipleTag) { // Non-multiple annotations will be returned only once
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