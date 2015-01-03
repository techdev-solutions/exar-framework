<?php
namespace Exar\TestClasses;

/**
 * @NoValue
 * @FloatValue(1.5)
 * @StringValue(value='string value')
 * @ArrayValue({'a', 'b', 'c'})
 * @ArrayValue({1, 2, 3})
 * @Annotation\With\Namespace(value={'a', true, null})
 */
class ClassAnnotatedClass {
}
