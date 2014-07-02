<?php
namespace Exar\Annotation\Matcher;

class RegexMatcher implements MatcherInterface {
	private $pattern;

	public function __construct($pattern) {
		$this->pattern = $pattern;
	}

	public function match(&$str) {
		if (preg_match("/^{$this->pattern}/", $str, $matches) && $matches[0] != '') {
			$str = substr($str, strlen($matches[0]));
			return $matches[0];
		}

		throw new StringNotMatchedException($this, $str, $this->pattern);
	}
}
