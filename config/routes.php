<?php
/**
 * Application routes.
 *
 * All routes are arrays with the following three elements:
 * $method, $routePattern, $handler.
 *
 * The $handler should be appropriately namespaced.
 * Example: ['GET', '/', 'App\Core\Page/index']
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

return [
    'routes' => [
        // General content pages (also includes /).
        ['GET', '/[{uri}]', 'App\Content\Page/display']
    ]
];
