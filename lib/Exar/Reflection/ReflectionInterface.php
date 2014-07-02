<?php
namespace Exar\Reflection;

interface ReflectionInterface {

	public function hasAnnotation($annotation);

	public function getAnnotation($annotation);

	public function getAnnotationMap();
	
	public function getAnnotations($considerMultipleFlag = false);
}