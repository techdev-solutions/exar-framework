<?php
namespace Exar\TestClasses\Annotation;

use Exar\Annotation\Annotation;
use Exar\Aop\Interceptor\Interfaces\AfterReturningInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterThrowingInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterInvocationInterceptor;
use Exar\Aop\Interceptor\Interfaces\BeforeInvocationInterceptor;
use Exar\Aop\InvocationContext;

/**
 * @Target("class")
 */
class CustomAnnotation extends Annotation {

}
