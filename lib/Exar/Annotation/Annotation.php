<?php
namespace Exar\Annotation;

use Exar\Reflection\ReflectionClass;

/**
 * Base class for all annotations.
 */
abstract class Annotation {
    private $_allowValueProperty = true;
    private $_targetMeta;
    protected $_name;

    protected $value; // annotation value (used only for single-value annotations)

    /**
     * Constructor.
     *
     * @param $data annotation parameter as they are defined on the target object
     * @param \Reflector $target reflection object
     */
    public function __construct($data, \Reflector $target) {
        $rClass = new \ReflectionClass(get_class($this));
        $this->_name = $rClass->getShortName();

        $this->_targetMeta = self::extractTargetMeta($target);

        $rClass = new \ReflectionClass($this);

        if (isset($data['value']) && !$this->isAllowValueProperty()) {
            trigger_error('Property "value" ist not allowed on annotation "'.$rClass->getName().'"', E_USER_ERROR);
        }

        foreach($data as $key => $value) {
            if ($key == 'value' || ($rClass->hasProperty($key) && $rClass->getProperty($key)->getDeclaringClass()->getName() != __CLASS__)) {
                if ($rClass->getProperty($key)->isPrivate()) {
                    trigger_error('Property "'.$key.'" within annotation "'.$rClass->getName().'" is private and cannot be set', E_USER_ERROR);
                }
                $this->$key = $value;
            } else {
                trigger_error('Property "'.$key.'" ist not allowed on annotation "'.$rClass->getName().'"', E_USER_ERROR);
            }

        }
        $this->checkTargetAnnotation($target);
        $this->checkCreationConstraints();
    }

    /**
     * Returns true if this is a single-value annotation (anonymous attribute is activated), else false.<br/>
     * This method can be overridden to define multi-value annotation classes.
     *
     * @return bool if this is a single-value annotation, else false
     */
    public function isAllowValueProperty() {
        return $this->_allowValueProperty;
    }

    /**
     * Returns the target class.
     *
     * @return string target class
     */
    public function getTargetClass() {
        return $this->_targetMeta['class'];
    }

    /**
     * Return the target method
     *
     * @return string target method
     */
    public function getTargetMethod() {
        return $this->_targetMeta['method'];
    }

    /**
     * Returns the annotation name.
     *
     * @return string annotation name
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Checks the annotation constraints.<br/>
     * This method can be overridden to define specific conditions (e.g. certain method modifiers).
     *
     * @param $targetObject annotation's target
     */
    public function checkConstraints($targetObject) {}

    /**
     * This method can be used at runtime to decide if the target method of the annotation has to be intercepted.<br/>
     * This will be needed for class annotations that propagates specific behavior for one or more methods.
     *
     * @param $methodName method name
     * @return bool true if the specified method is applied to this annotation, else false
     */
    public function acceptMethod($methodName) {
        return true;
    }

    protected function setAllowValueProperty($allowValueProperty) {
        $this->_allowValueProperty = $allowValueProperty;
    }

    protected function setTargetClass($class) {
        $this->_targetMeta['class'] = $class;
    }

    protected function setTargetMethod($method) {
        $this->_targetMeta['method'] = $method;
    }

    protected function checkCreationConstraints() {}

    protected static function checkBooleanConstraint($val, $errorMsg = null) {
        if (!$val) {
            self::triggerError($errorMsg);
        }
    }

    protected static function triggerError($errorMsg = null) {
        trigger_error($errorMsg === null ? 'Annotation constraint violation' : $errorMsg, E_USER_ERROR);
    }

    private function checkTargetAnnotation($target) {
        $rClass = new ReflectionClass(get_class($this));
        if ($rClass->hasAnnotation('Target')) { // @Target is set
            $value = $rClass->getAnnotation('Target')->value; // read the value attribute of @Target
            $values = is_array($value) ? $value : array($value); // convert string value into an array if needed
            foreach($values as $value) {
                $value = strtolower($value);
                if ($value == 'class' && $target instanceof \ReflectionClass) return;
                if ($value == 'method' && $target instanceof \ReflectionMethod) return;
                if ($value == 'property' && $target instanceof \ReflectionProperty) return;
            }
            trigger_error('Annotation "'.get_class($this).'" is not allowed on "'.$target->getName().'"', E_USER_ERROR);
        }
    }

    private static function extractTargetMeta($target) {
        $meta = array();

        if ($target instanceof \ReflectionClass) {
            $meta['class'] = '\\'.$target->getName();
            $meta['method'] = '*';
        } elseif ($target instanceof \ReflectionMethod) {
            $meta['class'] = '\\'.$target->getDeclaringClass()->getName();
            $meta['method'] = $target->getName();
        } elseif ($target instanceof \ReflectionProperty) {
            $meta['class'] = '\\'.$target->getDeclaringClass()->getName();
            $meta['method'] = '__construct';
        } else {
            trigger_error('Illegal target type ('.get_class($target).')', E_USER_ERROR);
        }

        return $meta;
    }
}