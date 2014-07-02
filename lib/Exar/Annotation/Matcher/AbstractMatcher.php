<?php
namespace Exar\Annotation\Matcher;

abstract class AbstractMatcher implements MatcherInterface {
	protected $matchers = array();

	public function add(MatcherInterface $matcher) {
		$this->matchers[] = $matcher;
	}
}
