<?php

    require_once("../vendor/autoload.php");
    use App\Controllers\UsuarioController;

    $controller = new UsuarioController;
    $result = $controller->getPage();

?>
<!DOCTYPE html>
<html lang="pt-br" class="html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="css/usuario.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" />

    <title>Usuário</title>
</head>
<body>
    
    <input type="hidden" id="base-url" value="<?php echo BASE_URL; ?>">
    <input type="hidden" id="paypal-tipo" value="<?php echo PAYPAL_TIPO; ?>">
    <?php
        if($result['success']){
            $usuario = $result['dados'];
    ?>

            <div class="profile-card">
                <div class="image">
                    <img src="img/profile.png" alt="" class="profile-img" />
                </div>

                <div class="text-data">
                    <span class="name"><?php echo $usuario['nome']; ?> </span>
                    <span class="job"><?php echo $usuario['profissao']; ?></span>
                </div>

                <div class="credit-box">
                    <!--<i class="bx bx-coins"></i>-->
                    <div class="credit-img">
                        <img src="img/coins.svg" />
                    </div>
                    <div class="credit-value">
                        <?php echo $usuario['creditos_formatado']; ?>
                    </div>
                </div>

                <div class="media-buttons">
                    <a href="#" style="background: #4267b2;" class="link">
                        <i class="bx bxl-facebook"></i>
                    </a>
                    <a href="#" style="background: #1da1f2;" class="link">
                        <i class="bx bxl-twitter"></i>
                    </a>
                    <a href="#" style="background: #e1306c;" class="link">
                        <i class="bx bxl-instagram"></i>
                    </a>
                    <a href="#" style="background: #ff0000;" class="link">
                        <i class="bx bxl-youtube"></i>
                    </a>
                </div>

                <!--<div class="buttons">
                    <button class="button">Subscribe</button>
                    <button class="button">Message</button>
                </div>-->

                <div class="analytics">
                    <div class="data">
                        <i class="bx bx-heart"></i>
                        <span class="number">40k</span>
                    </div>
                    <div class="data">
                        <i class="bx bx-message-rounded"></i>
                        <span class="number">15k</span>
                    </div>
                    <div class="data" id="btn-credit">
                        <i class="bx bx-plus"></i>
                        <span class="number">CREDIT</span>
                    </div>
                </div>
            </div>
            <script type="text/javascript">

                const baseUrl = document.querySelector('#base-url').value;
                const paypalTipo = document.querySelector('#paypal-tipo').value;
                const btnCredit = document.querySelector('#btn-credit');

                btnCredit.addEventListener('click', () => {
                    if(paypalTipo == "ipn"){
                        window.location.href = baseUrl+"/pages/comprar-ipn.php";
                    }else if(paypalTipo == "order"){
                        window.location.href = baseUrl+"/pages/comprar-order.php";
                    }else{
                        alert("Paypal tipo "+paypalTipo+" inválido");
                    }
                });

            </script>
    <?php
        }else{
    ?>
            <div class="container-erro">
                <div class="info">
                    <p>
                        <b>Erro: </b><?php echo $result['msg']; ?>
                    </p>
                </div>
            </div>
    <?php
        }
    ?>
    
</body>
</html>