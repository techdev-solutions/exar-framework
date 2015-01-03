<?php
namespace Exar\Aop\Interceptor\Interfaces;

use Exar\Aop\InvocationContext;

interface BeforeInvocationInterceptor {
    public function beforeInvocation(InvocationContext $context);
}