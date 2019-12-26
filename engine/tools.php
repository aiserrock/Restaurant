<?php
function get_route($route, $routes): string
{
    return isset($routes[$route]) ? "engine/pages/{$routes[$route]}.php" : "engine/pages/404.php";
}

//функция убирает дубликаты и складывает кол-во
function sumQuantity(&$dishes, &$quantity)
{
    for ($i = 0; $i < count($dishes); $i++) {
        for ($j = $i + 1; $j < count($dishes); $j++) {
            if ($dishes[$i] == $dishes[$j]) {
                $quantity[$i] += $quantity[$j];
                unset($dishes[$j], $quantity[$j]);
            }
        }
    }
}

//проводит биекцию между двумя массивами


function merge($dishes, $quantity): array
{
    if (count($dishes) != count($quantity))
        return [];

    for ($i = 0; $i < count($dishes); $i++) {
        $result[$dishes[$i]] = $quantity[$i];
    }
    return $result;
}