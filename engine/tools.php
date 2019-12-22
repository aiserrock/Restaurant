<?php
function get_route($route, $routes): string
{
    return isset($routes[$route]) ? "engine/pages/{$routes[$route]}.php" : "engine/pages/404.php";
}
