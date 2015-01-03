<?php
namespace Exar\Annotation\Interceptor;

use Exar\Exception;

/**
 * Exception class for interruptions of interceptor executions.<br/>
 * It is used to immediately stop interceptor actions and should only be used by generated code.
 */
class InterceptorException extends Exception {
	private $object;
	private $result;

	/**
	 * Constructor.
	 * 
	 * @param string $message exception message
	 * @param object $obj object in which exception was thrown
	 * @param mixed $result result to set
	 */
	public function __construct($message, $obj, $result = null) {
		parent::__construct($message);
		$this->object = $obj;
		$this->result = $result;
	}

	/**
	 * Returns the object where the exception was thrown.
	 *
	 * @return object object that threw this exception
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * Result of the method execution where the exception was thrown.
	 *
	 * @return mixed|null result of the method execution or null if there is no result available
	 */
	public function getResult() {
		return $this->result;
	}
}