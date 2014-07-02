<?php
namespace Exar\Annotation\Matcher;

class SingleValueMatcher extends ValueMatcher {
	public function match(&$str) {
		$toParse = $str;
		$value = parent::match($toParse);
		$str = $toParse;
		return array('value' => $value);
	}
}
