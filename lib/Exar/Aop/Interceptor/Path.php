<?php
namespace Exar\Aop\Interceptor;

use Exar\Annotation\Annotation;

/**
 * @Target("method")
 */
class Path extends Annotation {
	protected function checkCreationConstraints() {
		$rClass = new \ReflectionClass($this->getTargetClass());
		$rMethod = $rClass->getMethod($this->getTargetMethod());

		$errorCause = null;
		if (!$rMethod->isPublic()) {
			$errorCause = 'non-public method';
		}

		if ($rMethod->isAbstract()) {
			$errorCause = 'abstract method';
		}

		if ($rMethod->isStatic()) {
			$errorCause = 'static method';
		}

		if ($rMethod->isConstructor()) {
			$errorCause = 'constructor';
		}

		if ($rMethod->isDestructor()) {
			$errorCause = 'destructor';
		}

		if ($errorCause !== null) {
			trigger_error('Annotation "'.get_class($this).'" is not allowed on '.$errorCause.' "'.$this->getTargetMethod().'"', E_USER_ERROR);
		}
	}

	public function getValue() {
		return $this->value;
	}

}
