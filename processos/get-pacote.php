<?php

    require_once("../vendor/autoload.php");

    use App\Controllers\PacoteController;

    $controller = new PacoteController;
    $response = $controller->get();
    echo json_encode($response);

?>