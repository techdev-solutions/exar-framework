<?php
namespace Exar\Aop\Interceptor;

use Exar\Annotation\Annotation;
use Exar\Aop\Interceptor\Interfaces\AfterInvocationInterceptor;
use Exar\Aop\InvocationContext;

/**
 * Interceptor for class methods which sets the Content-Type header.
 *
 * @Target("method")
 */
class MediaType extends Annotation implements AfterInvocationInterceptor {
	public function afterInvocation(InvocationContext $context, $result) {
		header('Content-Type: ' . $this->value);
		return $result;
	}
}
