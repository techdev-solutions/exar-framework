<?php
namespace Exar\Reflection;

/**
 * Interface for extended Reflection API.
 */
interface ReflectionInterface {
    /**
     * Returns true if an annotation for the specified name is present on this element, else false.
     *
     * @param $name annotation name
     * @return bool true if an annotation for the specified annotation name is present on this element, else false
     */
    public function hasAnnotation($name);

    /**
     * Returns this element's annotation for the specified name if such an annotation is present, else null.
     *
     * @param $name annotation name
     * @return mixed this element's annotation (last entry) for the specified annotation name if present on this element, else null
     */
    public function getAnnotation($name);

    /**
     * Returns all annotations (as an associative array with annotation name as key and arrays of annotation
     * objects as values) present on this element. (Returns an empty array if this element has no annotations).
     *
     * @return array all annotations present on this element
     */
    public function getAnnotationMap();

    /**
     * Returns all annotations (as a single-dimensional array) present on this element.
     *
     * @param bool $considerMultipleFlag if true, duplicated annotation names are eliminated if they are not tagged with @Multiple, otherwise every all annotations are returned
     * @return array single-dimensional array with annotations present on this element
     */
    public function getAnnotations($considerMultipleTag = false);
}