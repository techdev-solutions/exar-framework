<?php
namespace Exar\Annotation\Matcher;

class StringNotMatchedException extends \Exception {
	private $matcher;
	private $str;
	private $strToMatch;
	
	public function __construct($matcher, $str, $strToMatch) {
		$this->matcher = $matcher;
		$this->str = $str;
		$this->strToMatch = $strToMatch;
	}
	
	public function getStr() {
		return $this->str;
	}
	
	public function getStrToMatch() {
		return $this->strToMatch;
	}
	
	public function __toString() {
		return get_class($this->matcher).' - String not matched: expected ['.$this->strToMatch.'], actual ['.$this->str.']';
	}
}
