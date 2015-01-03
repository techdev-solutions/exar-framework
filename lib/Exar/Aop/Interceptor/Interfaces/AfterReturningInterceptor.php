<?php
namespace Exar\Aop\Interceptor\Interfaces;

use Exar\Aop\InvocationContext;

interface AfterReturningInterceptor {
    public function afterReturning(InvocationContext $context, $result);
}