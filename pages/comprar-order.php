<?php

    require_once("../vendor/autoload.php");
    use App\Controllers\VendaController;

    $controller = new VendaController;
    $result = $controller->getPageComprar();

?>
<!DOCTYPE html>
<html lang="pt-br" class="html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/comprar.css">
    <title>Paypal</title>
</head>
<body>
    <input type="hidden" id="base_url" value="<?php echo BASE_URL; ?>">
    <div class="container">
        <form id="comprar_form" action="" method="POST">
            <div class="row">
                <div class="column">
                    <h3 class="title">COMPRAR PACOTE</h3>
                    <div class="input-box">
                        <span>Pacote</span>
                        <select id="pacote_id" name="pacote_id">
                            <?php
                                if($result['success']){
                                    foreach($result['dados']['pacotes'] as $pacote){
                                        echo "<option value=\"{$pacote['id']}\">{$pacote['nome']}</option>";
                                    }
                                }else{
                                    echo "<option value=\"\">Nenhum pacote encontrado</option>";
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="input-box">
                        <span>Usuário</span>
                        <input type="text" id="username" name="username" placeholder="username" required>
                    </div>
                    <div class="input-box">
                        <span>Email</span>
                        <input type="email" id="email" name="email" placeholder="example@example.com" required>
                    </div>
                    <div class="flex">
                        <div class="input-box">
                            <span>Valor</span>
                            <input type="text" id="valor" placeholder="R$ 0,00" readonly>
                        </div>
                        <div class="input-box">
                            <span>Créditos</span>
                            <input type="text" id="creditos" placeholder="0000" readonly>
                        </div>
                        <div class="img-box">
                            <img src="img/paypal.jpg">
                        </div>
                    </div>
                </div>

            </div>
            <button type="submit" class="btn">COMPRAR</button>
        </form>
        
    </div>
    <script type="text/javascript" src="js/funcoes.js"></script>
    <script type="text/javascript" src="js/comprar_order.js"></script>
</body>
</html>