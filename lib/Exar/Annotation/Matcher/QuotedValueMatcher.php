<?php
namespace Exar\Annotation\Matcher;

class QuotedValueMatcher extends ParallelMatcher {
	public function __construct() {
		$this->add(new RegexMatcher(AnnotationsMatcher::REGEX_PARAMETER_SINGLE_QUOTED_VALUE));
		$this->add(new RegexMatcher(AnnotationsMatcher::REGEX_PARAMETER_DOUBLE_QUOTED_VALUE));
	}

	public function match(&$str) {
		$value = parent::match($str);
		return substr($value, 1, strlen($value) - 2);
	}
}
