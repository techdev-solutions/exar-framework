<?php
namespace Exar\Annotation\Matcher;

use Exar\ExarTest;

class ArrayMatcherTest extends ExarTest {
    public function testArrays() {
        $m = new ArrayMatcher();

        $str = '{}'; // empty array
        $this->assertEmpty($m->match($str));

        $str = '{123}'; // simple array with one element
        $this->assertEquals(array(123), $m->match($str));

        $str = '    {    345  }        '; // white spaces
        $this->assertEquals(array(345), $m->match($str));

        $str = '{1,3,5}'; // multiple values
        $this->assertEquals(array(1, 3, 5), $m->match($str));

        $str = '{"a", \'b\', 3.14, 1000, NULL, false}'; // different value types
        $this->assertEquals(array('a', 'b', 3.14, 1000, null, false), $m->match($str));

        $str = '{1, { 2, 3, 4 }, 5}'; // nested arrays
        $this->assertEquals(array(1, array(2, 3, 4), 5), $m->match($str));

        $str = '{6, {}, 7}'; // nested arrays
        $this->assertEquals(array(6, array(), 7), $m->match($str));
    }

    /**
     * @expectedException Exar\Annotation\Matcher\StringNotMatchedException
     */
    public function testAssocArray() {
        $m = new ArrayMatcher();
        $str = '{"a" => 23}';
        $m->match($str);
    }

    /**
     * @expectedException Exar\Annotation\Matcher\StringNotMatchedException
     */
    public function testBadArrayValue() {
        $m = new ArrayMatcher();
        $str = '{1,}';
        $m->match($str);
    }
}
 