<?php
namespace Exar\TestClasses;

/**
 * @Exar
 * @CorrectAnnotation
 * This is a comment which contains other annotations like @Foo or @Bar.
 * But these annot@tions are not recognized during @nnotation parsing.
 * @AnotherCorrectAnnotation
 */
class ClassWithComments extends ClassWithConstructorParams {
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
