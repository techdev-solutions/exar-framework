<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ClassWithParentConstructor extends ClassWithConstructorParams {
    private $anotherValue = null;

    public function getAnotherValue() {
        return $this->anotherValue;
    }

    /**
     * @PostConstruct
     */
    public function anotherInit() {
        $this->anotherValue = 789;
    }
}
