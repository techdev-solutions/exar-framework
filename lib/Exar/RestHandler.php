<?php
namespace Exar;

use Exar\Reflection\ReflectionClass;
use Exar\Reflection\ReflectionMethod;

/**
 * REST handler class that supports basic REST functionality.
 * The resource classes do not have to be annotated with @Exar.
 * The action methods must be annotated with @Path to be processed.
 *
 * IMPORTANT: To get your RESTful services working, you have to tell the web server
 * to route all REST calls to the PHP script where your resources are dispatched.
 * Here's an .htaccess file for Apache:
 *      RewriteEngine on
 *      RewriteRule . index.php
 *
 * Then, you register the resource classes within index.php:
 *      \Exar\RestHandler::dispatch(array('\My\Rest\Resource'));
 */
class RestHandler {
    /** 1xx */
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;

    /** 2xx */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;
    const HTTP_IM_USED = 226;

    /** 4xx */
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIME_OUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_LOCKED = 423;
    const HTTP_FAILED_DEPENDENCY = 424;
    const HTTP_UPGRADE_REQUIRED = 426;

    /** 5xx */
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIME_OUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    const HTTP_INSUFFICIENT_STORAGE = 507;
    const HTTP_NOT_EXTENDED = 510;

    /**
     * Array with HTTP status codes
     * @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    static private $status = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        510 => 'Not Extended'
    );

    static private $routeCollector = null;
    static private $restDispatcher = null;

    static public function dispatch(array $classes) {
        self::$routeCollector = new \FastRoute\RouteCollector(
            new \FastRoute\RouteParser\Std(),
            new \FastRoute\DataGenerator\GroupCountBased()
        );

        self::activate($classes);

        self::$restDispatcher = new \FastRoute\Dispatcher\GroupCountBased(self::$routeCollector->getData());

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $routeInfo = self::$restDispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                self::sendResponseCode(self::HTTP_NOT_FOUND);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                self::sendResponseCode(self::HTTP_METHOD_NOT_ALLOWED);
                break;
            case \FastRoute\Dispatcher::FOUND:
                self::sendResponseCode(self::HTTP_OK);
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $handlerParts = explode('::', $handler);
                $obj = new $handlerParts[0];
                echo call_user_func_array(array($obj, $handlerParts[1]), $vars);
                break;
        }

    }

    static public function sendResponseCode($num) {
        $protocol = empty($_SERVER['SERVER_PROTOCOL']) ? 'HTTP/1.1' : $_SERVER['SERVER_PROTOCOL'];

        $str = array_key_exists($num, self::$status) ? self::$status[$num] : '';
        $header = "$protocol $num $str";

        header($header, true, false);
    }

    static private function activate(array $classes) {
        foreach ($classes as $cl) {
            $rClass = new ReflectionClass($cl);

            foreach ($rClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $rMethod) {
                self::processMethod($rMethod);
            }
        }
    }

    static private function processMethod(ReflectionMethod $method) {
        if (!$method->hasAnnotation('Path')) { // method must be annotated with @Path to be processed
            return;
        }

        $path = $method->getAnnotation('Path');
        self::$routeCollector->addRoute(self::getHttpMethod($method), $path->getValue(), $method->getDeclaringClass()->getName().'::'.$method->getName());
    }

    static private function getHttpMethod(ReflectionMethod $method) {
        foreach (array('GET', 'PUT', 'POST', 'DELETE') as $ann) {
            if ($method->hasAnnotation($ann)) {
                return $ann;
            }
        }
        return 'GET';
    }

}