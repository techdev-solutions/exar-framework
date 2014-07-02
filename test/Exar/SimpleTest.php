<?php
namespace Exar;

/**
 * Abstract class with a simple autoloader.
 * Test classes which inherit from this class cannot test AOP features.
 *
 * @package Exar
 */
abstract class SimpleTest extends \PHPUnit_Framework_TestCase {
    static protected $cacheDir;

    public static function setUpBeforeClass() {
        $srcDir = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        set_include_path($srcDir . PATH_SEPARATOR . get_include_path());

        self::$cacheDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_cache'; // set cache directory
        self::cleanCache();
    }

    /**
     * Delete all files within the cache directory.
     */
    private static function cleanCache() {
        #array_map('unlink', glob(self::$cacheDir . '/*'));
    }
}