<?php
namespace Exar\TestClasses\Annotation;

use Exar\Annotation\Annotation;

/**
 * @Target("class")
 * @Multiple
 */
class MultipleAnnotation extends Annotation {
    public function getValue() {
        return $this->value;
    }
}
