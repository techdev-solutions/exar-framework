<?php
namespace Exar\Annotation\Matcher;

class ParallelMatcher extends AbstractMatcher {
	public function match(&$str) {
		$str = trim($str); // ignore whitespaces
		foreach ($this->matchers as $matcher) {
			$toParse = $str;
			try {
				$result = $matcher->match($toParse);
				$str = $toParse;
				return $result; // return first result that was parsed successfully
			} catch (StringNotMatchedException $e) {
				// do nothing
			}
		}
		throw new StringNotMatchedException($this, $str, '<UNKNOWN>');
	}
}
