<?php
namespace Exar;

/**
 * Exar autoloader for classes annotated with @Exar.
 */
class Autoloader {
    const NAMESPACE_SEPARATOR = '\\';
    const EXAR_ANNOTATION_NAMESPACE = '\\Exar\\Aop\\Interceptor'; // namespace for Exar annotations/interceptors

    static private $cacheDir = null; // path to cache directory
    static private $namespaces = array('Exar'); // registered namespaces for Exar annotated classes
    static private $annotationNamespaces = array(); // registered annotation namespaces
    static private $weaver = null; // class weaver object

    /**
     * Registers this autoloader on the autoloader queue.
     *
     * @param $cacheDir the path to cache directory
     * @param array $namespaces array with nmespaces to be loaded with this autoloader
     * @param bool $prepend if true, this autoloader will be prepended on the autoload queue, otherwise it is appended
     */
    static public function register($cacheDir, array $namespaces, $prepend = true) {
        self::$cacheDir = $cacheDir;
        self::$namespaces = array_merge(self::$namespaces, $namespaces);
        self::$annotationNamespaces = array(self::EXAR_ANNOTATION_NAMESPACE);
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
    }

    /**
     * Loads the specified class and processes the annotations if the namespace of the class matches registered namespaces.
     *
     * @param $class
     * @return bool true if the specified class has the @Exar annotation and was loaded, otherwise false
     */
    static public function autoload($class) {
        $pos = strrpos($class, self::NAMESPACE_SEPARATOR, 1);

        $hasSupportedNamespace = false;
        if ($pos !== false) {
            $namespace = ltrim(substr($class, 0, $pos), self::NAMESPACE_SEPARATOR);
            foreach (self::$namespaces as $ns) {
                if (strpos($namespace, $ns) === 0) { // the specified class is within a supported (i.e. registered) namespace
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

    /**
     * Returns the weaver object.
     *
     * @return Aop\Weaver the weaver object (will be instantiated on the first method call)
     */
    static private function getWeaver() {
        if (self::$weaver === null) { // the weaver is not yet instantiated
            self::$weaver = new \Exar\Aop\Weaver(self::$cacheDir);
        }
        return self::$weaver;
    }

    /**
     * Returns the cache directory of this autoloader.
     *
     * @return string the path ot the cache directory
     */
    static public function getCacheDir() {
        return self::$cacheDir;
    }

    /**
     * Returns all registered namespaces.
     *
     * @return array registered namespaces
     */
    static public function getNamespaces() {
        return self::$namespaces;
    }

    /**
     * Adds annotation namespaces.
     *
     * @param $ns a namespace or an array of namespaces to add
     */
    static public function addAnnotationNamespaces($ns) {
        if (!is_array($ns)) {
            $ns = array($ns);
        }
        self::$annotationNamespaces = array_merge(self::$annotationNamespaces, $ns);
    }

    /**
     * Returns all registered annotation namespaces.
     *
     * @return array annotation namespaces
     */
    static public function getAnnotationNamespaces() {
        return self::$annotationNamespaces;
    }

    /**
     * Deletes all files within the cache directory.
     */
    static public function cleanCache() {
        array_map('unlink', glob(self::$cacheDir . '/*'));
    }

}