<?php
namespace Exar;

class Autoloader {
	const NAMESPACE_SEPARATOR = '\\';

    static private $cacheDir = null;
	static private $namespaces = array('Exar');
    static private $weaver = null;

	static public function register($cacheDir, array $namespaces, $prepend = true) {
        self::$cacheDir = $cacheDir;
		self::$namespaces = array_merge(self::$namespaces, $namespaces);
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
    }
	
	static public function autoload($class) {
		$pos = strrpos($class, self::NAMESPACE_SEPARATOR, 1);

        $hasSupportedNamespace = false;
        if ($pos !== false) {
			$namespace = ltrim(substr($class, 0, $pos), self::NAMESPACE_SEPARATOR);
            foreach(self::$namespaces as $ns) {
                if (strpos($namespace, $ns) === 0) {
                    $hasSupportedNamespace = true;
                    break;
                }
            }
		}

        if (!$hasSupportedNamespace) { // return if namespace is not supported
            return false;
        }

        $file = stream_resolve_include_path(strtr(ltrim($class, self::NAMESPACE_SEPARATOR), self::NAMESPACE_SEPARATOR, '/') . '.php');
        if ($file) {
            if ($class == 'Exar\\Aop\\Weaver') {
                return require_once $file;
            } else {
                return self::getWeaver()->process($file);
            }
        }
	}

    static private function getWeaver() {
        if (self::$weaver === null) {
            self::$weaver = new \Exar\Aop\Weaver(self::$cacheDir);
        }
        return self::$weaver;
    }

    /**
     * Return the cache directory of this autoloader.
     */
    static public function getCacheDir() {
        return self::$cacheDir;
    }

    /**
     * Delete all files within the cache directory.
     */
    static public function cleanCache() {
        array_map('unlink', glob(self::$cacheDir . '/*'));
    }
	
}