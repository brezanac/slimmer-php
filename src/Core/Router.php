<?php

namespace App\Core;

use App\Exceptions\RouterException;
use App\Content\Page;
use FastRoute;

/**
 * Deals with routing requests to their handlers.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class Router
{

    public function __construct()
    {

    }

    public function init()
    {
        // Load routes from the configuration.
        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $routes = Config::get('routes');
            foreach ($routes as $route) {
                list($method, $pattern, $handler) = $route;
                $r->addRoute($method, $pattern, $handler);
            }
        });

        // Dispatching the actual request.
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'])['path']);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        try {
            switch ($routeInfo[0]) {
                case FastRoute\Dispatcher::NOT_FOUND:
                    /*
                     * The dispatched route responded with a 404 Not Found code.
                     * NOTICE: Due to a greedy routing rule in config/routes.php
                     * the 404 response needs to be dispatched in the Page class.
                     */
                    throw new RouterException(RouterException::ROUTE_NOT_FOUND, [
                        'httpMethod' => $httpMethod, 'URI' => $uri
                    ]);
                    break;

                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    // The dispatched route method is not allowed.
                    $allowedMethods = $routeInfo[1];
                    throw new RouterException(RouterException::ROUTE_METHOD_NOT_ALLOWED, [
                        'httpMethod' => $httpMethod, 'allowedMethods' => $allowedMethods
                    ]);
                    break;

                case FastRoute\Dispatcher::FOUND:
                    // Route successfully dispatched.
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    list($class, $method) = explode("/", $handler, 2);
                    call_user_func_array(array(new $class, $method), $vars);
                    break;
            }
        } catch (RouterException $e) {
            Log::log($e->getMessage(), $e->getSeverity(), $e->getContext());
        }
    }
}
