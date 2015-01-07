<?php
namespace Exar\Aop;

use Exar\Annotation\SimpleAnnotation;

use Exar\Annotation\Annotation;
use Exar\Aop\Interceptor\Interfaces\AfterThrowingInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterReturningInterceptor;
use Exar\Aop\Interceptor\Interfaces\AfterInvocationInterceptor;
use Exar\Aop\Interceptor\Interfaces\BeforeInvocationInterceptor;

/**
 * Singleton class where interceptors are registered. This singleton obejct also invokes the interceptor methods.
 */
class InterceptorManager {
    private static $instance = null; // singleton instance

    private $annotations = array();

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {}

    public function registerAnnotations(array $annotations) {
        foreach ($annotations as $annotation) {
            self::registerAnnotation($annotation);
        }
    }

    /**
     * Registers an annotation (the information about target object is extracted from the annotation object).
     *
     * @param Annotation $annotation annotation to register
     */
    public function registerAnnotation(Annotation $annotation) {
        $className = $annotation->getTargetClass();
        $methodName = $annotation->getTargetMethod();

        if (!isset($this->annotations[$className])) {
            $this->annotations[$className] = array();
        }

        if (!isset($this->annotations[$className][$methodName])) {
            $this->annotations[$className][$methodName] = array();
        }

        $this->annotations[$className][$methodName][] = $annotation;
    }

    /**
     * Executes registered before advices (before the advised method is invoked).
     *
     * @param InvocationContext $context invocation context
     */
    public function before(InvocationContext $context) {
        foreach ($this->detectInterceptorsForContext($context) as $interceptor) {
            if ($interceptor instanceof BeforeInvocationInterceptor) {
                $interceptor->beforeInvocation($context);
            }
        }
    }

    /**
     * Executes registered after throwing advices (after the advised method throws an exception during execution).
     *
     * @param InvocationContext $context invocation context
     */
    public function afterThrowing(InvocationContext $context) {
        foreach ($this->detectInterceptorsForContext($context) as $interceptor) {
            if ($interceptor instanceof AfterThrowingInterceptor) {
                $interceptor->afterThrowing($context);
            }
        }
    }

    /**
     * Executes registered after returning advices (after the advised method finishes execution without exceptions).
     *
     * @param InvocationContext $context invocation context
     * @param $result result returned from the advised method after its invocation
     */
    public function afterReturning(InvocationContext $context, $result) {
        foreach ($this->detectInterceptorsForContext($context) as $interceptor) {
            if ($interceptor instanceof AfterReturningInterceptor) {
                $interceptor->afterReturning($context, $result);
            }
        }
    }

    /**
     * Executes registered after advices (after the advised method finishes execution).
     *
     * @param InvocationContext $context invocation context
     * @param $result result returned from the advised method after its invocation
     * @return mixed method result that is returned after it has been processed by registered interceptors
     */
    public function after(InvocationContext $context, $result) {
        foreach ($this->detectInterceptorsForContext($context) as $interceptor) {
            if ($interceptor instanceof AfterInvocationInterceptor) {
                $result = $interceptor->afterInvocation($context, $result);
            }
        }
        return $result;
    }

    private function detectInterceptorsForContext(InvocationContext $context) {
        $methodName = $context->getMethodName();
        if ($methodName === null) {
            $methodName = '*';
        }

        $className = $context->getClassName();
        if (!preg_match('/^\\\\/', $className)) {
            $className = '\\'.$className;
        }

        $classNameAnnotations = $this->annotations[$className];

        if (!isset($classNameAnnotations)) {
            return array();
        }

        if (!array_key_exists($methodName, $classNameAnnotations)) {
            $classNameAnnotations[$methodName] = array();
        }

        if (!array_key_exists('*', $classNameAnnotations)) {
            $classNameAnnotations['*'] = array();
        }

        $detectedInterceptors = array();
        if ($context->getMethodName() == '__construct') {
            $detectedInterceptors = $classNameAnnotations[$methodName];
        } else {
            $detectedInterceptors = array_merge($classNameAnnotations['*'], $classNameAnnotations[$methodName]);
        }

        /** select interceptors which accept the name of the called method */
        $result = array();
        array_walk($detectedInterceptors, function ($interceptor) use ($context, &$result) {
            if (!($interceptor instanceof  SimpleAnnotation) && $interceptor->acceptMethod($context->getMethodName())) {
                $interceptor->checkConstraints($context->getTarget());
                $result[] = $interceptor;
            }
        });
        return $result;
    }
}
