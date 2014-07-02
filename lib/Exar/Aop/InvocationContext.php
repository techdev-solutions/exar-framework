<?php
namespace Exar\Aop;

class InvocationContext {
	private $target;
	private $className;
	private $methodName;
	private $params;

    /**
     * @var \Exception
     */
    private $exception = null;

	public function __construct($target, $className, $methodName, array $params) {
		$this->target = $target;
		$this->className = $className;
		$this->methodName = $methodName;
		$this->params = $params;
	}

	public function getTarget() {
		return $this->target;
	}

	public function setParam($name, $value) {
		$this->params[$name] = $value;
	}
	
	public function setParams(array $params) {
		$this->params = $params;
	}

	public function getParams() {
		return $this->params;
	}

	public function getClassName() {
		return $this->className;
	}

	public function getMethodName() {
		return $this->methodName;
	}

	public function setException(\Exception $e) {
		$this->exception = $e;
	}

    /**
     * @return \Exception
     */
    public function getException() {
		return $this->exception;
	}

	public function deleteException() {
		$this->exception = null;
	}

	public function hasException() {
		return $this->exception !== null;
	}

}