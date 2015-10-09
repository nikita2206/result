<?php

use Result\Result as R;

require __DIR__ . "/vendor/autoload.php";

function divide($a, $b)
{
    if ($b === 0 || $b === .0) {
        return R::err("division_by_zero");
    }

    return R::ok($a / $b);
}

function calculateSomething($a, $b, $c)
{
    $errMapper = function ($err) { return "calculation_error"; };

    // $x now has a value of expression ($a / $b)
    // meaning Result returned from divide was already unwrapped for you
    $x = (yield divide($a, $b));

    // you can also remap errors if it's needed
    $x = (yield divide($x, $c)->remapErr($errMapper));

    yield R::ok($x + 42);
}

$res = R::reduce(calculateSomething(1, 2, 3));
var_dump($res);

$res = R::reduce(calculateSomething(1, 0, 1));
var_dump($res);

$res = R::reduce(calculateSomething(1, 2, 0));
var_dump($res); // the error was remapped, now it will be "calculation_error"
