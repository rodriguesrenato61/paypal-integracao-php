<?php

    namespace App\Controllers;

    use App\Repositories\PacoteRepository;
    use App\Database\DbConexao;
    use App\Services\VendaService;
    use App\Database\Tbls\TblPaypalCompra;
    use App\Repositories\PaypalCompraRepository;

    class VendaController {

        public function getPageComprar(){

            try {

                $pacoteRepository = new PacoteRepository();
                $pacotes = $pacoteRepository->findAll(DbConexao::ROW_ARRAY);
                if(count($pacotes) == 0){
                    $pacotes[] = [
                        'id' => "",
                        'nome' => "Nenhum pacote encontrado"
                    ];
                }

                return [
                    'success' => true,
                    'msg' => "Página de compra carregada com sucesso",
                    'dados' => [
                        'pacotes' => $pacotes
                    ]
                ];
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao carregar página de compra: ".$e->getMessage()
                ];
            }

        }

        public function comprar(){

            try {

                $form = [
                    'pacote_id' => (isset($_POST['pacote_id'])) ? filter_input(INPUT_POST, 'pacote_id', FILTER_SANITIZE_NUMBER_INT) : NULL,
                    'username' => (isset($_POST['username'])) ? trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS)) : NULL,
                    'email' => (isset($_POST['email'])) ? trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : NULL,
                    'tipo' => PAYPAL_TIPO
                ];

                $errors = [];
                if(empty($form['pacote_id'])){
                    $errors[] = "Pacote não identificado";
                }
                if(empty($form['username'])){
                    $errors[] = "Usuário não identificado";
                }
                if(empty($form['email'])){
                    $errors[] = "Email não identificado";
                }
                $tipos_validos = [TblPaypalCompra::TIPO_IPN, TblPaypalCompra::TIPO_ORDER];
                if(!in_array($form['tipo'], $tipos_validos)){
                    $errors[] = "Tipo inválido";
                }

                if(count($errors) > 0){
                    return [
                        'success' => false,
                        'msg' => $errors[0],
                        'errors' => $errors
                    ];
                }

                $service = new VendaService;
                return $service->comprar($form);
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao realizar compra do pacote: ".$e->getMessage()
                ];
            }

        }

        public function getPagePagamento(){

            try {

                $cod = (isset($_GET['cod'])) ? filter_input(INPUT_GET, 'cod', FILTER_SANITIZE_SPECIAL_CHARS) : NULL;
                if(empty($cod)){
                    return [
                        'success' => false,
                        'msg' => "Código de pagamento não identificado"
                    ];
                }

                $paypalCompraRepository = new PaypalCompraRepository();
                $paypalCompra = $paypalCompraRepository->findByViewCodigoReferencia($cod);
                if(empty($paypalCompra)){
                    return [
                        'success' => false,
                        'msg' => "Paypal compra não encontrado"
                    ];
                }

                $paypalCompra['valor_formatado'] = number_format(floatval($paypalCompra['valor']), 2, ',', '.');
                $paypalCompra['creditos_formatado'] = number_format(floatval($paypalCompra['creditos']), 0, ',', '.');
                $paypalCompra['data_registro'] = (isset($paypalCompra['data_atualizacao_br']) && !empty($paypalCompra['data_atualizacao_br'])) ? $paypalCompra['data_atualizacao_br'] : $paypalCompra['data_criacao_br'];

                switch($paypalCompra['payment_status']){
                    case "New":
                        $paypalCompra['status_nome'] = "Não pago";
                    break;
                    case "Completed":
                        $paypalCompra['status_nome'] = "Pago";
                    break;
                    case "APPROVED":
                        $paypalCompra['status_nome'] = "Pago";
                    break;
                    default:
                        $paypalCompra['status_nome'] = $paypalCompra['payment_status'];
                    break;
                }

                return [
                    'success' => true,
                    'msg' => "Paypal compra encontrado com sucesso",
                    'dados' => $paypalCompra
                ];
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao carregar página de pagamento: ".$e->getMessage()
                ];
            }

        }

    }

?>