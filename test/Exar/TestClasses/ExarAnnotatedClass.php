<?php
namespace Exar\TestClasses;

/**
 * @Exar
 */
class ExarAnnotatedClass {
    /**
     * @A
     */
    public function publicMethod() {
    }

    /**
     * @B
     */
    final public function publicFinalMethod() {
    }

    /**
     * @C
     */
    static public function publicStaticMethod() {
    }

    /**
     * @D
     */
    protected function protectedMethod() {
    }

    /**
     * @E
     */
    final protected function protectedFinalMethod() {
    }

    /**
     * @F
     */
    static protected function protectedStaticMethod() {
    }

    /**
     * @G
     */
    private function privateMethod() {
    }

    /**
     * @H
     */
    final private function privateFinalMethod() {
    }

    /**
     * @I
     */
    static private function privateStaticMethod() {
    }
}
