<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ClassWithParentsParentConstructor extends NoExarButInheritsFromExarClass {
    private $anotherValue = null;

    public function getAnotherValue() {
        return $this->anotherValue;
    }

    /**
     * @PostConstruct
     */
    public function anotherInit() {
        $this->anotherValue = 654;
    }
}
