<?php
namespace Exar\Aop\Interceptor;

use Exar\Annotation\Annotation;
use Exar\Aop\Interceptor\Interfaces\AfterReturningInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterThrowingInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterInvocationInterceptor;
use Exar\Aop\Interceptor\Interfaces\BeforeInvocationInterceptor;
use Exar\Aop\InvocationContext;

/**
 * @Target("method")
 * @Multiple
 */
class Track extends Annotation implements BeforeInvocationInterceptor, AfterInvocationInterceptor, AfterThrowingInterceptor, AfterReturningInterceptor {
	public function beforeInvocation(InvocationContext $context) {
		echo $this->createMessage($context, 'Before invocation');
	}

	public function afterReturning(InvocationContext $context, $result) {
		echo $this->createMessage($context, 'After returning');
		return $result;
	}

	public function afterThrowing(InvocationContext $context) {
		echo $this->createMessage($context, 'After throwing');
	}

	public function afterInvocation(InvocationContext $context, $result) {
		echo $this->createMessage($context, 'After invocation');
		return $result;
	}
	
	private function createMessage(InvocationContext $context, $prefix = '') {
		if ($this->value === null) {
			return $prefix.': '.$context->getClassName().'->'.$context->getMethodName().' ('.date('d.m.Y H:i:s', time()).')'.PHP_EOL;
		}
		return $this->value;
	}
}
