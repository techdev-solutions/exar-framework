<?php
namespace Exar\Annotation\Matcher;

class SequentialMatcher extends AbstractMatcher {
    protected $matchers = array();

    public function match(&$str) {
        $toParse = $str; // will be changed on every successful matching
        $result = array();
        foreach ($this->matchers as $matcher) { // loop over matchers, maintain matcher order
            $toParse = trim($toParse); // ignore whitespaces

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
