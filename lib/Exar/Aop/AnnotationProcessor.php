<?php
namespace Exar\Aop;

use Exar\Aop\InterceptorManager;

class AnnotationProcessor {
    const CLASSNAME = __CLASS__;

    public static function processAnnotations($class) {
        /** class annotations */
        $rClass = new \Exar\Reflection\ReflectionClass($class);
        InterceptorManager::getInstance()->registerAnnotations($rClass->getAnnotations(true));

        /** method annotations */
        foreach ($rClass->getMethods() as $method) {
            InterceptorManager::getInstance()->registerAnnotations($method->getAnnotations(true));
        }

        /** property annotations */
        foreach ($rClass->getProperties() as $property) {
            InterceptorManager::getInstance()->registerAnnotations($property->getAnnotationMap());
        }
    }

}