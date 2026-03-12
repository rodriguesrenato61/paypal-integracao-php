<?php

    require_once("../vendor/autoload.php");
    use App\Controllers\VendaController;

    $controller = new VendaController;
    $result = $controller->getPagePagamento();

?>
<!DOCTYPE html>
<html lang="pt-br" class="html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pagamento.css">
    <title>Pagamento</title>
</head>
<body>
    <input type="hidden" id="base_url" value="<?php echo BASE_URL; ?>">
    <div class="container">
        <div class="box">
            <div class="row">
                <div class="column">
                    <h3 class="title">PAGAMENTO</h3>
                    <div class="info">
                        <?php
                            if($result['success']){
                                $paypalCompra = $result['dados'];
                                echo "<p>";
                                echo "<b>Código: </b>{$paypalCompra['codigo_referencia']}<br>";
                                echo "<b>Usuário: </b>{$paypalCompra['usuario_nome']}<br>";
                                echo "<b>Pacote: </b>{$paypalCompra['descricao']}<br>";
                                echo "<b>Valor: </b>R$ {$paypalCompra['valor_formatado']}<br>";
                                echo "<b>Créditos: </b>{$paypalCompra['creditos_formatado']}<br>";
                                echo "<b>Status: </b>{$paypalCompra['status_nome']}<br>";
                                echo "<b>Data: </b>{$paypalCompra['data_registro']}";
                            }else{
                                echo "<p>";
                                echo "<b>Erro: </b>{$result['msg']}";
                                echo "</p>";
                            }
                        ?>
                        
                    </div>
                </div>
            </div>
            <a href="usuario.php" class="btn">
                <div class="btn-box">VOLTAR</div>
            </a>
        </div>
    </div>
</body>
</html>