<?php
namespace Exar\Annotation\Interceptor;

/**
 * Exception class for interruptions of interceptor executions.<br/>
 * It is used to immediately stop interceptor actions and should only be used by generated code.
 */
use Exar\Exception;

class InterceptorException extends Exception {
	private $object;
	private $result;

	/**
	 * Constructor.
	 * 
	 * @param string $message Exception message
	 * @param object $obj Object in which exception was thrown
	 * @param mixed $result Result to set
	 */
	public function __construct($message, $obj, $result = null) {
		parent::__construct($message);
		$this->object = $obj;
		$this->result = $result;
	}
	
	public function getObject() {
		return $this->object;
	}

	public function getResult() {
		return $this->result;
	}
}