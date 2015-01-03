<?php
namespace Exar\Reflection;

use Exar\SimpleTest;
use Exar\Aop\Weaver;

class WeaverTest extends SimpleTest {
    static protected $cacheDir;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::$cacheDir = \Exar\Autoloader::getCacheDir();
    }

    public function testCacheDir() {
        $weaver = new Weaver(self::$cacheDir);
        $this->assertEquals(self::$cacheDir, $weaver->getCacheDir());
    }

    public function testCacheDirDoesNotExist() {
        try {
            $weaver = new Weaver(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_folder_does_not_exist');
        } catch(\InvalidArgumentException $e) {
            $this->assertStringStartsWith('Cache directory does not exist: ', $e->getMessage());
        }
    }

    public function testCacheDirNotADir() {
        try {
            $weaver = new Weaver(__FILE__);
        } catch(\InvalidArgumentException $e) {
            $this->assertStringStartsWith('Cache directory is not a directory: ', $e->getMessage());
        }
    }

    public function testWeaverFileDoesNotExist() {
        $weaver = new Weaver(self::$cacheDir);
        try {
            $weaver->process(__FILE__ . '.does.not.exist');
        } catch(\InvalidArgumentException $e) {
            $this->assertStringStartsWith('File does not exist: ', $e->getMessage());
        }
    }

    public function testWeaverNoExarClass() {
        $weaver = new Weaver(self::$cacheDir);
        $weaver->process(__DIR__ . '/TestClasses/NoExarAnnotatedClass.php');

        $this->assertEquals(0, count(glob(self::$cacheDir . '/NoExarAnnotatedClass.php*')));
    }

    public function testWeaver() {
        $weaver = new Weaver(self::$cacheDir);

        $file = __DIR__ . '/TestClasses/ExarAnnotatedClass.php';
        $weaver->process($file);

        $this->assertEquals(1, count(glob(self::$cacheDir . '/ExarAnnotatedClass___' . md5($file) . '.php')));

        $rClass = new \ReflectionClass('Exar\TestClasses\ExarAnnotatedClass');

        $this->assertEquals(17, count($rClass->getMethods())); // 17 methods are expecting, including 2 methods which handle object construction

        /** public */
        $this->assertTrue($rClass->hasMethod('publicMethod'));
        $this->assertTrue(strpos($rClass->getMethod('publicMethod')->getDocComment(), '@A') !== false);
        $this->assertTrue($rClass->hasMethod('publicMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('publicMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** public final */
        $this->assertTrue($rClass->hasMethod('publicFinalMethod'));
        $this->assertTrue(strpos($rClass->getMethod('publicFinalMethod')->getDocComment(), '@B') !== false);
        $this->assertTrue($rClass->hasMethod('publicFinalMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('publicFinalMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** public static */
        $this->assertTrue($rClass->hasMethod('publicStaticMethod'));
        $this->assertTrue(strpos($rClass->getMethod('publicStaticMethod')->getDocComment(), '@C') !== false);
        $this->assertFalse($rClass->hasMethod('publicStaticMethod' . Weaver::METHOD_NAME_SUFFIX)); // no static methods are wrapped

        /** protected */
        $this->assertTrue($rClass->hasMethod('protectedMethod'));
        $this->assertTrue(strpos($rClass->getMethod('protectedMethod')->getDocComment(), '@D') !== false);
        $this->assertTrue($rClass->hasMethod('protectedMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('protectedMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** protected final */
        $this->assertTrue($rClass->hasMethod('protectedFinalMethod'));
        $this->assertTrue(strpos($rClass->getMethod('protectedFinalMethod')->getDocComment(), '@E') !== false);
        $this->assertTrue($rClass->hasMethod('protectedFinalMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('protectedFinalMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** protected static */
        $this->assertTrue($rClass->hasMethod('protectedStaticMethod'));
        $this->assertTrue(strpos($rClass->getMethod('protectedStaticMethod')->getDocComment(), '@F') !== false);
        $this->assertFalse($rClass->hasMethod('protectedStaticMethod' . Weaver::METHOD_NAME_SUFFIX)); // no static methods are wrapped

        /** private */
        $this->assertTrue($rClass->hasMethod('privateMethod'));
        $this->assertTrue(strpos($rClass->getMethod('privateMethod')->getDocComment(), '@G') !== false);
        $this->assertTrue($rClass->hasMethod('privateMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('privateMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** private final */
        $this->assertTrue($rClass->hasMethod('privateFinalMethod'));
        $this->assertTrue(strpos($rClass->getMethod('privateFinalMethod')->getDocComment(), '@H') !== false);
        $this->assertTrue($rClass->hasMethod('privateFinalMethod' . Weaver::METHOD_NAME_SUFFIX));
        $this->assertFalse($rClass->getMethod('privateFinalMethod' . Weaver::METHOD_NAME_SUFFIX)->getDocComment());

        /** private static */
        $this->assertTrue($rClass->hasMethod('privateStaticMethod'));
        $this->assertTrue(strpos($rClass->getMethod('privateStaticMethod')->getDocComment(), '@I') !== false);
        $this->assertFalse($rClass->hasMethod('privateStaticMethod' . Weaver::METHOD_NAME_SUFFIX)); // no static methods are wrapped
    }

}
