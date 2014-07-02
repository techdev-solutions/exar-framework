<?php
namespace Exar\Annotation\Matcher;

class ParallelMatcher extends AbstractMatcher {
	public function match(&$str) {
		$str = trim($str);
		foreach ($this->matchers as $matcher) {
			$toParse = $str;
			try {
				$result = $matcher->match($toParse);
				$str = $toParse;
				return $result;
			} catch (StringNotMatchedException $e) {
				// do nothing
			}
		}
		throw new StringNotMatchedException($this, $str, '<UNKNOWN>');
	}
}
