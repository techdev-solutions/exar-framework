<?php
namespace Exar\Aop;

class Weaver {
    const METHOD_NAME_SUFFIX = '___exar___generated';
    const NAMESPACE_SEPARATOR = '\\';

    private $parser;
    private $prettyPrinter;
    private $cacheDir;

    public function __construct($cacheDir = null) {
        if ($cacheDir === null) {
            $cacheDir = \Exar\Autoloader::getCacheDir();
        }

        $this->parser = new \PhpParser\Parser(new \PhpParser\Lexer);
        $this->prettyPrinter = new \PhpParser\PrettyPrinter\Standard;

        /* check cache directory */
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777);
        }

        $this->cacheDir = realpath($cacheDir);

        if (!is_dir($this->cacheDir)) {
            throw new \InvalidArgumentException('Cache directory is not a directory: ' . $this->cacheDir);
        }

        if (!is_writable($this->cacheDir)) {
            throw new \InvalidArgumentException('Cache directory is not writable: ' . $this->cacheDir);
        }

    }

    public function getCacheDir() {
        return $this->cacheDir;
    }

    public function process($file) {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File does not exist: ' . $file);
        }

        $code = file_get_contents($file);

        try {
            $stmts = $this->parser->parse($code);

            $classes = array();
            foreach ($stmts as $stmt) {
                $classes = $this->processStmt($stmt);
            }

            if (count($classes) == 0) { // no properly annotated classes found, so just include the file
                require_once $file;
                return true;
            }

            $newCode = '<?php' . PHP_EOL . $this->prettyPrinter->prettyPrint($stmts) . PHP_EOL . PHP_EOL;

            $pathInfo = pathinfo($file);
            $cachedFileName = $this->cacheDir . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '___' . md5($file) . '.' . $pathInfo['extension'];

            foreach($classes as $cl) {
                $newCode .= self::NAMESPACE_SEPARATOR . AnnotationProcessor::CLASSNAME . '::processAnnotations("\\' . $cl . '");' . PHP_EOL;
            }

            file_put_contents($cachedFileName, $newCode);

            require_once $cachedFileName;
            return true;

        } catch (PhpParser\Error $e) {
            // TODO handle parse error
        }
    }

    private function processStmt(\PhpParser\Node\Stmt $stmt, $namespace = null) {
        $classes = array();

        if ($stmt instanceof \PhpParser\Node\Stmt\Class_) { // class definition found
            $className = $this->processClass($stmt, $namespace);
            if ($className) {
                $classes[] = $namespace . self::NAMESPACE_SEPARATOR . $className;
            }
        } else {
            if ($stmt instanceof \PhpParser\Node\Stmt\Namespace_) {
                $namespace = implode(self::NAMESPACE_SEPARATOR, $stmt->name->parts);
            }
            $subNodes = $stmt->getSubNodeNames();
            if ($stmt->stmts !== null && in_array('stmts', $subNodes)) {
                foreach ($stmt->stmts as $s) {
                    if ($s instanceof \PhpParser\Node\Stmt) {
                        $classes = array_merge($classes, $this->processStmt($s, $namespace));
                    }
                }
            }
        }

        return $classes;
    }

    private function processClass(\PhpParser\Node\Stmt\Class_ $class, $namespace) {
        if (!strpos($class->getDocComment(), '@Exar')) { // no @Exar annotation found
            return false;
        }

        $wrapperMethods = array();
        $constructor = null;
        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof \PhpParser\Node\Stmt\ClassMethod) {
                if ($stmt->isStatic()) { // do not consider static methods
                    continue;
                }

                $wrapperStmts = $this->processClassMethod($stmt);
                if ($wrapperStmts !== null) {
                    $wrapperMethods[] = $wrapperStmts;

                    if($wrapperStmts->name == '__construct') {
                        $constructor = $stmt;
                    }
                }
            }
        }

        if ($constructor === null) { // there are no constructors defined within the current class
            $wrapperMethods = array_merge($wrapperMethods, $this->handleConstructors($class, $namespace, $constructor));
        }

        $class->stmts = array_merge($class->stmts, $wrapperMethods);
        return $class->name;
    }

    private function getClassWithConstructor($class) {
        if ($class->hasMethod('__construct')) { // constructor found
            return $class; // return reflection class which defines the constructor
        }

        if ($class->getParentClass()) { // parent class is available
            return $this->getConstructorParams($class->getParentClass()); // look in the parent class
        }

        return null; // no constructor found
    }

    private function processClassMethod(\PhpParser\Node\Stmt\ClassMethod $method, $callParentClass = false) {
        $newMethod = $this->generateMethodForClassMethod($method, $callParentClass);
        return $newMethod;
    }

    private function generateMethodForClassMethod(\PhpParser\Node\Stmt\ClassMethod $method, $callParentClass = true) {
        $modifiers = array();

        if ($method->isFinal()) { $modifiers[] = 'final'; }
        if ($method->isAbstract()) { $modifiers[] = 'abstract'; }
        if ($method->isStatic()) { $modifiers[] = 'static'; }
        if ($method->isPublic()) { $modifiers[] = 'public'; }
        if ($method->isProtected()) { $modifiers[] = 'protected'; }
        if ($method->isPrivate()) { $modifiers[] = 'private'; }

        $newMethod = clone $method;
        $newMethodBody = $this->generateWrapperMethodBody($method, $callParentClass);
        $newMethod->stmts = $this->parser->parse('<?php '.$newMethodBody);

        /** make the original method unreachable from outside */
        $method->type = $method->type & 56 | 4; // remove public/protected modifiers and make the method private
        $method->name .= self::METHOD_NAME_SUFFIX; // the original method gets suffix
        $method->setAttribute('comments', array()); // remove all comments from the original method

        return $newMethod;
    }

    private function generateWrapperMethodBody(\PhpParser\Node\Stmt\ClassMethod $method, $callParentClass = false) {
        $paramNames = array();
        foreach($method->params as $param) {
            $paramNames[] = $param->name;
        }
        return $this->generateMethodBody($method->name, $paramNames, $callParentClass);
    }

    private function generateMethodBody($methodName, $paramNames, $callParentClass = false) {
        $params = array();
        foreach ($paramNames as $p) {
            $params[] = "'".$p."'".'=>'.'$'.$p;
        }

        /*
         * name of the context, interceptor manager and the result var should be changed, otherwise there can be a name
         * collision if a parameter of the invoked wrapper method has the same name
         */
        $hash = uniqid();
        $ctxVar = '$ctx'.$hash;
        $imVar = '$im'.$hash;
        $resultVar = '$result'.$hash;

        if ($methodName == '__construct' && $callParentClass) {
            $methodCall = "'parent::__construct'";
        } else {
            $methodCall = "array(\$this, '".$methodName.self::METHOD_NAME_SUFFIX."')";
        }

        $code = "{$ctxVar} = new \\Exar\\Aop\\InvocationContext(\$this, __CLASS__, '{$methodName}', array(".implode(', ', $params)."));
            {$imVar} = \\Exar\\Aop\\InterceptorManager::getInstance();
            try {
                {$imVar}->before({$ctxVar});
                {$resultVar} = call_user_func_array({$methodCall}, array_values({$ctxVar}->getParams()));
                {$imVar}->afterReturning({$ctxVar}, {$resultVar});
            } catch (\\Exar\\Annotation\\Interceptor\\InterceptorException \$e) {
                if (\$e->getObject() === \$this) throw \$e;
                {$resultVar} = \$e->getResult();
            } catch (\\Exception \$e) {
                {$ctxVar}->setException(\$e);
                {$imVar}->afterThrowing({$ctxVar});
                if (!isset({$resultVar})) { {$resultVar} = null; }
            }
            {$resultVar} = {$imVar}->after({$ctxVar}, {$resultVar});
            if ({$ctxVar}->hasException()) { throw {$ctxVar}->getException(); }
            return {$resultVar};".PHP_EOL;

        return $code;
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $class
     * @param $namespace
     * @param $constructor
     * @return array
     */
    private function handleConstructors(\PhpParser\Node\Stmt\Class_ $class, $namespace, $constructor) {
        $wrapperMethods = array();

        $parentClassWithConstructor = null; // initially, assume that there is no parent class which defines constructor

        if ($class->extends !== null) { // look for constructor definition in parent classes
            $parentClass = new \ReflectionClass($namespace . self::NAMESPACE_SEPARATOR . $class->extends->getFirst());
            $parentClassWithConstructor = $this->getClassWithConstructor($parentClass);
        }

        $factory = new \PhpParser\BuilderFactory;
        $constructorNode = $factory->method('__construct')->makePublic();

        if ($parentClassWithConstructor !== null) { // add parameters to constructor (if any found)
            foreach ($parentClassWithConstructor->getMethod('__construct')->getParameters() as $p) {
                $param = $factory->param($p->getName());
                if ($p->isOptional()) {
                    $param->setDefault($p->getDefaultValue());
                }
                $constructorNode->addParam($param);
            }
        }

        $constructorNode = $constructorNode->getNode();

        if ($parentClassWithConstructor === null) { // add an empty constructor if there are no constructors available at all
            $wrapperMethods[] = $constructorNode;
        }

        $wrapperMethods[] = $this->processClassMethod($constructorNode, $parentClassWithConstructor !== null);

        return $wrapperMethods;
    }

}