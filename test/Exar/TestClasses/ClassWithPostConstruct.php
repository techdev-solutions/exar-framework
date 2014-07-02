<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ClassWithPostConstruct {
	private $value = null;

    function __construct() {
    }

    public function getValue() {
        return $this->value;
    }

    /**
     * @PostConstruct
     */
    public function init() {
        $this->value = 123;
    }
}
