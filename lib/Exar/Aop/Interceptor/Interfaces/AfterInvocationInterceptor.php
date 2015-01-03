<?php
namespace Exar\Aop\Interceptor\Interfaces;

use Exar\Aop\InvocationContext;

interface AfterInvocationInterceptor {
    public function afterInvocation(InvocationContext $context, $result);
}