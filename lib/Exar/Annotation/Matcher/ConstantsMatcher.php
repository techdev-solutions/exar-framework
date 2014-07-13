<?php
namespace Exar\Annotation\Matcher;

class ConstantsMatcher implements MatcherInterface {
	private static $mapping = null;
	
	public function __construct() {
		if (self::$mapping === null) { // initialize map when called for the first time
			self::$mapping = array(
				'true'	=> true,
				'TRUE'	=> true,
				'false'	=> false,
				'FALSE'	=> false,
				'null'	=> null,
				'NULL'	=> null
			);
		}
	}

	public function match(&$str) {
		foreach (self::$mapping as $key => $value) {
			if (preg_match("/^$key/", $str)) { // value matched
				$str = substr($str, strlen($key));
				return $value;
			}
		}
		throw new StringNotMatchedException($this, $str, '<ANY CONSTANT>');
	}
}
