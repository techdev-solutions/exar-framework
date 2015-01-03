<?php
namespace Exar\Annotation\Matcher;

abstract class AbstractMatcher implements Matcher {
    protected $matchers = array();

    public function add(Matcher $matcher) {
        $this->matchers[] = $matcher;
    }
}
