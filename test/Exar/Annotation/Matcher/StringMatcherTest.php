<?php
namespace Exar\Annotation\Matcher;

use Exar\ExarTest;

class StringMatcherTest extends ExarTest {
    public function testStrings() {
        $m = new StringMatcher('(');

        $str = '(123)';
        $this->assertEmpty($m->match($str));
        $this->assertEquals('123)', $str);

        $str = 'text';
        try {
            $m->match($str);
            $this->fail('An exception was expected');
        } catch(StringNotMatchedException $e) {
            $this->assertEquals('text', $str);
        }
    }

    /**
     * @expectedException Exar\Annotation\Matcher\StringNotMatchedException
     */
    public function testWhiteSpaces() {
        $m = new StringMatcher('(');
        $str = '   456   ';
        $m->match($str);
    }

    /**
     * @expectedException Exar\Annotation\Matcher\StringNotMatchedException
     */
    public function testStringTooLong() {
        $m = new StringMatcher('abcde');
        $str = 'abcd';
        $m->match($str);
    }
}
 