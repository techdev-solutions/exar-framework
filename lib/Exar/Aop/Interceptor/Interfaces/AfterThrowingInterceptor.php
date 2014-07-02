<?php
namespace Exar\Aop\Interceptor\Interfaces;

use Exar\Aop\InvocationContext;

interface AfterThrowingInterceptor {
	public function afterThrowing(InvocationContext $context);
}