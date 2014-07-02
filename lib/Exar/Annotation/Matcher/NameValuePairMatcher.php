<?php
namespace Exar\Annotation\Matcher;

class NameValuePairMatcher extends SequentialMatcher {
	public function __construct() {
		$this->add(new RegexMatcher(AnnotationsMatcher::REGEX_PARAMETER_NAME));
		$this->add(new StringMatcher('='));
		$this->add(new ValueMatcher());
	}

	public function match(&$str) {
		$arr = parent::match($str);
		return array($arr[0]=>$arr[1]);
	}
}
