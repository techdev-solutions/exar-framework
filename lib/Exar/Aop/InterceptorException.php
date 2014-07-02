<?php
namespace Exar\Annotation\Interceptor;

/**
 * Exception-Klasse zum Abbrechen von Interceptor-Ausf�hrungen.<br/>
 * Z.B. muss bei der <code>Secured</code>-Annotation die Ausf�hrung der gesch�tzten Action
 * abgebrochen werden, falls die Berechtigung seitens Nutzer fehlt. So wird diese Exception geworfen.
 * Sie bewirkt ausschlie�lich den Ausgang aus der Action und sollte nicht woanders verwendet werden.
 *
 * @author vwidiker
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