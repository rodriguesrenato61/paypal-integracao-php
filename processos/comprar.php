<?php

    require_once("../vendor/autoload.php");

    use App\Controllers\VendaController;

    $controller = new VendaController;
    $response = $controller->comprar();
    echo json_encode($response);

?>