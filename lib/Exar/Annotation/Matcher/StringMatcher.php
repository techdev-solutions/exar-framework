<?php
namespace Exar\Annotation\Matcher;

class StringMatcher implements Matcher {
	private $string;

	public function __construct($string = '') {
		$this->string = $string;
	}

	public function match(&$str) {
		$length = strlen($this->string);

		if(strlen($str) >= $length && substr($str, 0, $length) == $this->string) {
			$str = substr($str, $length);
			return array();
		}
		
		throw new StringNotMatchedException($this, $str, $this->string);
	}
}
