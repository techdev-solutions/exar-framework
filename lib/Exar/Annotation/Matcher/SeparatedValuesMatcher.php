<?php
namespace Exar\Annotation\Matcher;

class SeparatedValuesMatcher implements Matcher {
	private $matcherForSeparatedValues;

	public function __construct(Matcher $matcherForSeparatedValues) {
		$this->matcherForSeparatedValues = $matcherForSeparatedValues;
	}

	public function match(&$str) {
		$toParse = $str;
		$separatorMatcher = new StringMatcher(',');
		
		$result = array();

		while (true) {
			$toParse = trim($toParse);
			
			try {
				$value = $this->matcherForSeparatedValues->match($toParse);
				if ($this->matcherForSeparatedValues instanceof ValueMatcher) {
					$result[] = $value;
				} else {
					$result = array_merge($result, $value);
				}
			} catch (StringNotMatchedException $e) {
				throw $e;
			}

			try {
				$toParse = trim($toParse);
				$separatorMatcher->match($toParse);
			} catch (StringNotMatchedException $e) {
				$str = $toParse;
				return $result;
			}
		}
	}
}