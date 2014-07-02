<?php
namespace Exar\Annotation\Matcher;

class SequentialMatcher extends AbstractMatcher {
	protected $matchers = array();

	public function match(&$str) {
		$toParse = $str;
		$result = array();
		foreach ($this->matchers as $matcher) {
			$toParse = trim($toParse);
			
			$value = $matcher->match($toParse);
			if (is_array($value)) {
				if ($matcher instanceof ValueMatcher) $result[] = $value;
				else $result = array_merge($result, $value);
			} else {
				$result[] = $value;
			}
		}

		$str = $toParse;
		return $result;
	}
}
