<?php
namespace Exar\Annotation\Matcher;

class ValueMatcher extends ParallelMatcher {
    public function __construct() {
        $this->add(new ArrayMatcher());
        $this->add(new QuotedValueMatcher());
        $this->add(new ConstantsMatcher());
        $this->add(new NumberMatcher());
    }
}
