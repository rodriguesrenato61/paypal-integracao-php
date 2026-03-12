<?php

    require_once("../vendor/autoload.php");

    use App\Controllers\PaypalController;

    $controller = new PaypalController;
    $response = $controller->webhookIPN();
    echo json_encode($response);

?>