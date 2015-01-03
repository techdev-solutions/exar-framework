<?php
namespace Exar\TestClasses;

/**
 * @One("a")
 * @Two("b")
 * @Three("c")
 * @MultipleAnnotation("d")
 * @One("e")
 * @MultipleAnnotation("f")
 * @Two("g")
 * @MultipleAnnotation("h")
 * @One("i")
 */
class ClassAnnotatedClassForAnnotationContainer {
    const CLASSNAME = __CLASS__;
}
