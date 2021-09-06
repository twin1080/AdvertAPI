<?php
declare(strict_types=1);
use AdvertAPI\ResponseWrapper;
use Laminas\Diactoros\ServerRequestFactory;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$container = require __DIR__ . '/../config/container.php';
$routes = require __DIR__ . '/../config/routes.php';

$middleware = [
    new ResponseWrapper(),
    new FastRoute($routes),
    new RequestHandler($container)
];

//$requestHandler = new Relay($middleware);
$dispatcher = new \Middlewares\Utils\Dispatcher($middleware);
$response = $dispatcher->handle(ServerRequestFactory::fromGlobals());

(new SapiEmitter())->emit($response);












//declare(strict_types=1);
//require_once dirname(__DIR__) . '/vendor/autoload.php';
//
//$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
//    $r->addRoute('POST', '/ads', 'AdsController/createAction');
//    $r->addRoute('POST', '/user/{id:\d+}', 'AdsController/updateAction');
//    $r->addRoute('GET', '/ads/relevant', 'AdsController/showAction');
//});
//
//$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
//$response = (new \Laminas\Diactoros\ResponseFactory())->createResponse();
//$httpMethod = $_SERVER['REQUEST_METHOD'];
//$uri = $_SERVER['REQUEST_URI'];
//
//if (false !== $pos = strpos($uri, '?')) {
//    $uri = substr($uri, 0, $pos);
//}
//$uri = rawurldecode($uri);
//
//$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
//switch ($routeInfo[0]) {
//    case FastRoute\Dispatcher::NOT_FOUND:
//        $response = $response->withStatus(404);
//        $response->getBody()->write('oops!');
//        break;
//    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
//        $allowedMethods = $routeInfo[1];
//        $response = $response->withStatus(405);
//        $response->getBody()->write('method' . $allowedMethods . 'not allowed');
//        break;
//    case FastRoute\Dispatcher::FOUND:
//        $handler = $routeInfo[1];
//        $vars = $routeInfo[2];
//        $handler->
//        break;
//
//
//}
//
//http_response_code($response->getStatusCode());
//foreach ($response->getHeaders() as $header => $value) {
//    header($header . ':' . $value);
//}
//echo $response->getBody()->getContents();
//
//
