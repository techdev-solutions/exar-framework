<?php
namespace Exar\Annotation\Matcher;

use Exar\ExarTest;

class NumberMatcherTest extends ExarTest {
    public function testNumbers() {
        $m = new NumberMatcher();

        $str = '34abc';
        $result = $m->match($str);
        $this->assertTrue(is_integer($result));
        $this->assertEquals(34, $result);
        $this->assertEquals('abc', $str);

        $str = '3.14159-';
        $result = $m->match($str);
        $this->assertTrue(is_float($result));
        $this->assertEquals(3.14159, $result);
        $this->assertEquals('-', $str);

        $str = 'str';
        try {
            $m->match($str);
            $this->fail('An exception was expected');
        } catch(StringNotMatchedException $e) {
            $this->assertEquals('str', $str);
        }
    }

    /**
     * @expectedException Exar\Annotation\Matcher\StringNotMatchedException
     */
    public function testWhiteSpaces() {
        $m = new NumberMatcher('(');
        $str = '   654   ';
        $m->match($str);
    }
}
 