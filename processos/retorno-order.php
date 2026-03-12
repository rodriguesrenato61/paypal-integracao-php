<?php

    require_once("../vendor/autoload.php");

    use App\Controllers\PaypalController;

    $controller = new PaypalController;
    $controller->retornoOrder();

?>