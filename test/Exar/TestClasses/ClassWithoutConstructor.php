<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ClassWithoutConstructor {
    private $value = null;

    public function getValue() {
        return $this->value;
    }

    /**
     * @PostConstruct
     */
    public function init() {
        $this->value = 345;
    }
}
