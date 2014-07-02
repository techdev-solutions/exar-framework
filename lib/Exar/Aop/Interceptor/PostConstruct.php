<?php
namespace Exar\Aop\Interceptor;

use Exar\Reflection\ReflectionClass;
use Exar\Annotation\Annotation;
use Exar\Aop\Interceptor\Interfaces\AfterInvocationInterceptor;
use Exar\Aop\InvocationContext;

/** @Target("method") */
class PostConstruct extends Annotation implements AfterInvocationInterceptor {
	public function __construct($data, \Reflector $target) {
		parent::__construct($data, $target);
		$this->setTargetMethod('__construct');
		$this->setAllowValueProperty(false);
	}
	
	public function afterInvocation(InvocationContext $context, $result) {
		$rClass = new ReflectionClass($context->getClassName());
		foreach ($rClass->getMethods() as $method) {
			if ($method->hasAnnotation($this->getName())) {
				$methodToCall = $method->getName();
				$rMethod = $rClass->getMethod($methodToCall);
				parent::checkBooleanConstraint($rMethod->isPublic() && !$rMethod->isStatic() && count($rMethod->getParameters()) == 0);
				$context->getTarget()->$methodToCall();
			}
		}
		return $result;
	}
}
