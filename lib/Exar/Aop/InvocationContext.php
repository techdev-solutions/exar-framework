<?php
namespace Exar\Aop;

/**
 * Container class that encapsulates all information needed to intercept a call.<br/>
 * This class should only be used by generated code.
 */
class InvocationContext {
    private $target; // target object
    private $className; // target object's type
    private $methodName; // name of the method to intercept
    private $params; // parameters of the intercepted method
    private $exception = null; // exception thrown during method invocation

    /**
     * Constructor.
     *
     * @param $target target object
     * @param $className target object's type
     * @param $methodName name of the method to intercept
     * @param array $params parameters of the intercepted method
     */
    public function __construct($target, $className, $methodName, array $params) {
        $this->target = $target;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->params = $params;
    }

    /**
     * Return the target object.
     *
     * @return target object
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Sets a method parameter. This method can be used for subsequently manipulations on the method parameters.
     *
     * @param $name parameter name
     * @param $value parameter value
     */
    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * Overrides all method parameters. This method can be used for subsequently manipulations on the method parameters.
     *
     * @param array $params method parameters
     */
    public function setParams(array $params) {
        $this->params = $params;
    }

    /**
     * Returns the parameters of the intercepted method.
     *
     * @return array method parameters
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Returns the type of the target object.
     *
     * @return target target object's type
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Return the name of the intercepted method.
     *
     * @return name method name
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * Sets the exception object.
     *
     * @param \Exception $e exception object
     */
    public function setException(\Exception $e) {
        $this->exception = $e;
    }

    /**
     * Returns the exception thrown during the method invocation (if any available).
     *
     * @return \Exception exception object or null if no exception was thrown
     */
    public function getException() {
        return $this->exception;
    }

    /**
     * Removes the exception from invocation context. (Can be used to manipulate interception process in a deeper way).
     */
    public function deleteException() {
        $this->exception = null;
    }

    /**
     * Returns true if an exception was thrown during method invocation, else false.
     *
     * @return bool true if an exception was thrown during method invocation, else false
     */
    public function hasException() {
        return $this->exception !== null;
    }

}