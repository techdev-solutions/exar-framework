<?php
namespace Exar\Annotation\Matcher;

class NumberMatcher extends RegexMatcher {
    public function __construct() {
        parent::__construct(AnnotationsMatcher::REGEX_PARAMETER_NUMBER_VALUE);
    }

    public function match(&$str) {
        $result = parent::match($str);
        $result = strpos($result, '.') ? (float) $result : (int) $result;

        return $result;
    }
}
