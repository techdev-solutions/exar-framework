<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ClassWithConstructorParams {
    private $value = null;
    private $param1;
    private $param2;

    public function __construct($param1, $param2 = 'defaultValue') {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }

    public function getParam1() {
        return $this->param1;
    }

    public function getParam2() {
        return $this->param2;
    }

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
