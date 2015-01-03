<?php
namespace Exar\Annotation\Matcher;

class ArrayMatcher extends SequentialMatcher {
    public function match(&$str) {
        $this->matchers = array();

        $this->add(new StringMatcher('{'));

        $pMatcher = new ParallelMatcher();
        $pMatcher->add(new SeparatedValuesMatcher(new ValueMatcher())); // for arrays with at least 1 value
        $pMatcher->add(new StringMatcher()); // for empty arrays
        $this->add($pMatcher);

        $this->add(new StringMatcher('}'));

        $arr = parent::match($str);
        return $arr;
    }
}
