<?php

    namespace App\Services;

    use App\Api\PaypalIPNClientApi;
    use App\Api\PaypalOrderClientApi;
    use App\Repositories\PaypalCompraRepository;
    use App\Database\DbConexao;
    use App\Repositories\UsuarioRepository;
    use App\Database\Tbls\TblLogCredito;
    use App\Repositories\LogCreditoRepository;
    use App\Database\Tbls\TblLogWebhook;
    use App\Repositories\LogWebhookRepository;
    use App\Utils\ArquivoUtil;
    use App\Repositories\PaypalCredenciaisRepository;

    class PaypalService {

        private $orderClientApi;
        private $comprador;

        public function setComprador($comprador){
            $this->comprador = $comprador;
            return $this;
        }

        public function getComprador(){
            return $this->comprador;
        }

        public function createFormIPN($paypalCompra){

            $action = "";
            if(PAYPAL_AMBIENTE == "sandbox"){
                $action = "https://www.sandbox.paypal.com/cgi-bin/webscr";
            }else if(PAYPAL_AMBIENTE == "production"){
                $action = "https://www.paypal.com/cgi-bin/webscr";
            }else{
                return [
                    'success' => false,
                    'msg' => "Paypal ambiente inválido"
                ];
            }
            
            return [
                'success' => true,
                'msg' => "Paypal IPN form gerado com sucesso",
                'dados' => [
                    'action' => $action,
                    'cmd' => "_xclick",
                    'amount' => $paypalCompra->getValor(),
                    'business' => PAYPAL_EMAIL_BUSINESS,
                    'item_name' => $paypalCompra->getDescricao(),
                    'currency_code' => PAYPAL_CURRENCY_CODE,
                    'no_note' => 1,
                    'no_shipping' => 1,
                    'rm' => 1,
                    'custom' => $paypalCompra->getCodigoReferencia(),
                    'return' => BASE_URL."/pages/pagamento.php?cod=".$paypalCompra->getCodigoReferencia(),
                    'cancel_return' => BASE_URL."/pages/pagamento.php?cod=".$paypalCompra->getCodigoReferencia(),
                    'notify_url' => BASE_URL."/processos/webhook-ipn.php"
                ]
            ];
        }

        public function webhookIPN($post){

            $logDados = [
                'success' => false,
                'msg' => "Webhook Paypal IPN acessado",
                'post' => $post
            ];

            $pdo = DbConexao::getPdo();
            $commit = false;

            try {

                $form = [
                    'mc_gross' => (isset($post['mc_gross'])) ? filter_var($post['mc_gross'], FILTER_VALIDATE_FLOAT) : NULL,
                    'payment_status' => (isset($post['payment_status'])) ? filter_var($post['payment_status'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL,
                    'custom' => (isset($post['custom'])) ? filter_var($post['custom'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL,
                    'business' => (isset($post['business'])) ? filter_var($post['business'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL,
                    'txn_id' => (isset($post['txn_id'])) ? filter_var($post['txn_id'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL,
                    'receiver_email' => (isset($post['receiver_email'])) ? filter_var($post['receiver_email'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL,
                    'txn_type' => (isset($post['txn_type'])) ? filter_var($post['txn_type'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL
                ];

                $logDados['form'] = $form;
                $errors = [];

                if($form['mc_gross'] === false){
                    $errors[] = "mc_gross não identificado";
                }
                if(empty($form['payment_status'])){
                    $errors[] = "payment_status não identificado";
                }
                if(empty($form['custom'])){
                    $errors[] = "custom não identificado";
                }
                if(empty($form['business'])){
                    $errors[] = "business não identificado";
                }
                if(empty($form['txn_id'])){
                    $errors[] = "txn_id não identificado";
                }
                if(empty($form['receiver_email'])){
                    $errors[] = "receiver_email não identificado";
                }
                if(!empty($form['txn_type'])){
                    if($form['txn_type'] != "web_accept"){
                        $errors[] = "txn_type inválido";
                    }
                }else{
                    $errors[] = "txn_type não identificado";
                }

                if(count($errors) > 0){
                    $logDados['msg'] = $errors[0];
                    $logDados['errors'] = $errors;
                    return [
                        'success' => false,
                        'msg' => $errors[0]
                    ];
                }

                $paypalIPNClientApi = new PaypalIPNClientApi();
                $verifyIPN = $paypalIPNClientApi->verify($post);
                $logDados['verify_IPN'] = $verifyIPN;
                if(!$verifyIPN['success']){
                    $logDados['msg'] = $verifyIPN['msg'];
                    return [
                        'success' => false,
                        'msg' => $verifyIPN['msg']
                    ];
                }

                if($form['business'] != PAYPAL_EMAIL_BUSINESS || $form['receiver_email'] != PAYPAL_EMAIL_BUSINESS){
                    $logDados['msg'] = "Business email ou receiver email não corresponde ao proprietário do email Paypal";
                    return [
                        'success'=> false,
                        'msg' => $logDados['msg']
                    ];
                }

                $paypalCompraRepository = new PaypalCompraRepository();
                $paypalCompra = $paypalCompraRepository->findByCodigoReferencia($form['custom'], DbConexao::ROW_OBJECT);
                if(empty($paypalCompra)){
                    $logDados['msg'] = "Paypal compra não encontrado";
                    return [
                        'success'=> false,
                        'msg' => $logDados['msg']
                    ];
                }

                $logDados['paypal_compra'] = $paypalCompra->toArray();

                if($paypalCompra->getAmbiente() != PAYPAL_AMBIENTE){
                    $logDados['msg'] = "Paypal compra ambiente ".$paypalCompra->getAmbiente()." não condiz com o ambiente configurado ".PAYPAL_AMBIENTE;
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }
                
                if($form['payment_status'] != $paypalCompra->getPaymentStatus()){
                    
                    $data_atual = strval(date('Y-m-d H:i:s'));

                    $paypalCompra->setTxnId($form['txn_id'])
                        ->setPaymentStatus($form['payment_status'])
                        ->setValorPago($form['mc_gross'])
                        ->setDataAtualizacao($data_atual);

                    $pdo->beginTransaction();

                    $logDados['paypal_compra_update'] = $paypalCompra->toArray();
                    $update = $paypalCompraRepository->update($paypalCompra);
                    if(!$update){
                        $logDados['msg'] = "Não foi possível atualizar Paypal compra";
                        return [
                            'success'=> false,
                            'msg' => $logDados['msg']
                        ];
                    }

                    if($paypalCompra->getPaymentStatus() == "Completed"){

                        $valor = floatval($paypalCompra->getValor());
                        if($form['mc_gross'] < $valor){
                            $commit = true;
                            $logDados['msg'] = "Valor pago não pode ser menor que o valor registrado";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $usuarioRepository = new UsuarioRepository();
                        $usuario = $usuarioRepository->findById($paypalCompra->getUsuarioId(), DbConexao::ROW_OBJECT);
                        if(empty($usuario)){
                            $logDados['msg'] = "Usuário não encontrado";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $logDados['usuario'] = $usuario->toArray();
                        $creditosAnterior = intval($usuario->getCreditos());
                        $creditosPosterior = $creditosAnterior + intval($paypalCompra->getCreditos());
                        $usuario->setCreditos($creditosPosterior)
                            ->setDataAtualizacao($data_atual);

                        $logDados['usuario_update'] = $usuario->toArray();
                        $update = $usuarioRepository->update($usuario);
                        if(!$update){
                            $logDados['msg'] = "Não foi possível atualizar créditos do usuário";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $logCreditoRepository = new LogCreditoRepository();
                        $logCredito = new TblLogCredito;
                        $logCredito->setUsuarioId($usuario->getId())
                            ->setPaypalCompraId($paypalCompra->getId())
                            ->setCreditoAnterior($creditosAnterior)
                            ->setCreditoPosterior($creditosPosterior)
                            ->setDataCriacao($data_atual);

                        $logDados['log_credito'] = $logCredito->toArray();
                        $insert = $logCreditoRepository->insert($logCredito);
                        if(!$insert){
                            $logDados['msg'] = "Não foi possível registrar log crédito";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $commit = true;
                        $logDados['success'] = true;
                        $logDados['msg'] = "Crédito adicionado com sucesso ao usuário";

                    }else{
                        $commit = true;
                        $logDados['success'] = true;
                        $logDados['msg'] = "Paypal compra atualizada para status {$form['payment_status']}";
                    }
                    
                }else{
                    $logDados['success'] = true;
                    $logDados['msg'] = "Paypal compra já foi processada";
                }

                return [
                    'success' => $logDados['success'],
                    'msg' => $logDados['msg']
                ];
            }catch(\Exception $e){
                $logDados['success'] = false;
                $logDados['msg'] = "Erro ao executar webhook do Paypal IPN: ".$e->getMessage();
                $commit = false;
                return [
                    'success' => false,
                    'msg' => $logDados['msg']
                ];
            }finally{

                if($pdo->inTransaction()){
                    if($commit){
                        $pdo->commit();
                    }else{
                        $pdo->rollBack();
                    }
                }

                $pdo = NULL;
                $sucesso = ($logDados['success']) ? "S" : "N";
                $paypal_compra_id = (isset($logDados['paypal_compra'])) ? $logDados['paypal_compra']['id'] : NULL;
                $msg = $logDados['msg'];
                unset($logDados['success']);
                unset($logDados['msg']);

                $resultLogArquivo = ArquivoUtil::criarArquivoLogWebhook($logDados);
                $arquivo = ($resultLogArquivo['success']) ? $resultLogArquivo['arquivo'] : NULL;

                $logWebhook = new TblLogWebhook;
                $logWebhook->setMensagem($msg)
                    ->setSucesso($sucesso)
                    ->setPaypalCompraId($paypal_compra_id)
                    ->setArquivo($arquivo);

                $logWebhookRepository = new LogWebhookRepository();
                $logWebhookRepository->insert($logWebhook);
            }

        }

        private function initOrderClientApi(){

            $repository = new PaypalCredenciaisRepository();
            $credenciais = $repository->get(DbConexao::ROW_OBJECT);
            if(empty($credenciais)){
                return [
                    'success' => false,
                    'msg' => "Credenciais Paypal Payment não encontradas!"
                ];
            }

            $refresh = false;
            if(!empty($credenciais->getDataExpiracao())){
                $data_atual = time();
                $data_expiracao = strtotime(date($credenciais->getDataExpiracao()));
                if($data_atual >= ($data_expiracao - 10)){
                    $refresh = true;
                }else{
                    $refresh = (empty($credenciais->getAccessToken()));
                }
            }else{
                $refresh = true;
            }

            $this->orderClientApi = new PaypalOrderClientApi(); 

            if($refresh){
                $result_auth = $this->orderClientApi->autenticar();
                if(!$result_auth['success']){
                    return $result_auth;
                }

                $response = $result_auth['response']['json'];
                $data_expiracao = strval(date('Y-m-d H:i:s', time() + intval($response['expires_in'])));
                $credenciais->setTokenTipo($response['token_type'])
                    ->setAccessToken($response['access_token'])
                    ->setDataExpiracao($data_expiracao)
                    ->setDataAtualizacao(strval(date('Y-m-d H:i:s')));

                $update = $repository->update($credenciais);
                if(!$update){
                    return [
                        'success' => false,
                        'msg' => "Não foi possível atualizar o novo token gerado Paypal Payment!",
                        'auth' => (isset($result_auth)) ? $result_auth : NULL
                    ];
                }
            }

            $this->orderClientApi->setTokenTipo($credenciais->getTokenTipo())
                ->setAccessToken($credenciais->getAccessToken());

            return [
                'success' => true,
                'msg' => "Autenticação na API do Paypal feita com sucesso!",
                'auth' => (isset($result_auth)) ? $result_auth : NULL
            ];
        }

        public function createOrder($paypalCompra){

            $initClientApi = $this->initOrderClientApi();
            if(!$initClientApi['success']){
                return $initClientApi;
            }

            $paypalCompra->setInvoiceId(uniqid());

            $request = [
                'intent' => "CAPTURE",
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => "IMMEDIATE_PAYMENT_REQUIRED",
                            'user_action' => "PAY_NOW",
                            'return_url' => BASE_URL."/processos/retorno-order.php?cod=".$paypalCompra->getCodigoReferencia(),
                            'cancel_url' => BASE_URL."/pages/retorno-order.php?cod=".$paypalCompra->getCodigoReferencia()
                        ]
                    ]
                ],
                'purchase_units' => [
                    [
                        'custom_id' => $paypalCompra->getCodigoReferencia(),
                        'invoice_id' => $paypalCompra->getInvoiceId(),
                        'amount' => [
                            'currency_code' => $paypalCompra->getCurrencyCode(),
                            'value' => $paypalCompra->getValor(),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => $paypalCompra->getCurrencyCode(),
                                    'value' => $paypalCompra->getValor()
                                ],
                                'shipping' => [
                                    'currency_code' => $paypalCompra->getCurrencyCode(),
                                    'value' => "0.00"
                                ]
                            ]
                        ],
                        'items' => [
                            [
                                'name' => $paypalCompra->getDescricao(),
                                'description' => $paypalCompra->getDescricao()." ({$this->comprador})",
                                'unit_amount' => [
                                    'currency_code' => $paypalCompra->getCurrencyCode(),
                                    'value' => $paypalCompra->getValor()
                                ],
                                'quantity' => "1",
                                'category' => "DIGITAL_GOODS",
                                'sku' => $paypalCompra->getPacoteId()
                            ]
                        ]
                    ]
                ]
            ];

            $createOrder = $this->orderClientApi->createOrder($request);
            if(!$createOrder['success']){
                return $createOrder;
            }
            $response = $createOrder['response']['json'];
            $paypalCompra->setOrderId($response['id'])
                ->setPaymentStatus($response['status'])
                ->setDataAtualizacao(strval(date('Y-m-d H:i:s')));

            $paypalCompraRepository = new PaypalCompraRepository();
            $update = $paypalCompraRepository->update($paypalCompra);
            if(!$update){
                return [
                    'success' => false,
                    'msg' => "Não foi possível atualizar Paypal Compra após criação do Paypal Order",
                    'paypal_compra' => $paypalCompra->toArray()
                ];
            }

            $link = "";
            for($i = 0; $i < count($response['links']); $i++){
                if($response['links'][$i]['method'] == "GET" && $response['links'][$i]['rel'] == "payer-action"){
                    $link = $response['links'][$i]['href'];
                    break;
                }
            }

            return [
                'success' => true,
                'msg' => "Paypal Order criado com sucesso",
                'dados' => [
                    'link' => $link
                ]
            ];
        }

        public function retornoOrder($request){

            $logDados = [
                'success' => false,
                'msg' => "Retorno Order Paypal acessado",
                'request' => $request
            ];

            $pdo = DbConexao::getPdo();
            $commit = false;

            try {

                $cod = (isset($request['get']['cod'])) ? filter_var($request['get']['cod'], FILTER_SANITIZE_SPECIAL_CHARS) : NULL;
                if(empty($cod)){
                    $logDados['msg'] = "Código de referência não identificado";
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }
                
                $paypalCompraRepository = new PaypalCompraRepository();
                $paypalCompra = $paypalCompraRepository->findByCodigoReferencia($cod, DbConexao::ROW_OBJECT);
                if(empty($paypalCompra)){
                    $logDados['msg'] = "Paypal compra não encontrado";
                    return [
                        'success'=> false,
                        'msg' => $logDados['msg']
                    ];
                }

                $logDados['paypal_compra'] = $paypalCompra->toArray();
                if(empty($paypalCompra->getOrderId())){
                    $logDados['msg'] = "Order Id do Paypal Compra não pode ser vazio";
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }

                if($paypalCompra->getAmbiente() != PAYPAL_AMBIENTE){
                    $logDados['msg'] = "Paypal compra ambiente ".$paypalCompra->getAmbiente()." não condiz com o ambiente configurado ".PAYPAL_AMBIENTE;
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }

                $initClientApi = $this->initOrderClientApi();
                if(!$initClientApi['success']){
                    $logDados['msg'] = $initClientApi['msg'];
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }

                $findOrder = $this->orderClientApi->findOrder($paypalCompra->getOrderId());
                $logDados['find_order'] = $findOrder;
                if(!$findOrder['success']){
                    $logDados['msg'] = "Erro ao consultar Paypal Order";
                    return [
                        'success' => false,
                        'msg' => $logDados['msg']
                    ];
                }

                $response = $findOrder['response']['json'];
                $valor_pago = floatval($response['purchase_units'][0]['amount']['value']);
                
                if($response['status'] != $paypalCompra->getPaymentStatus()){
                    
                    $data_atual = strval(date('Y-m-d H:i:s'));

                    $paypalCompra->setPaymentStatus($response['status'])
                        ->setValorPago($valor_pago)
                        ->setDataAtualizacao($data_atual);

                    $pdo->beginTransaction();

                    $logDados['paypal_compra_update'] = $paypalCompra->toArray();
                    $update = $paypalCompraRepository->update($paypalCompra);
                    if(!$update){
                        $logDados['msg'] = "Não foi possível atualizar Paypal compra";
                        return [
                            'success'=> false,
                            'msg' => $logDados['msg']
                        ];
                    }

                    if($paypalCompra->getPaymentStatus() == "APPROVED"){

                        $valor = floatval($paypalCompra->getValor());
                        if($valor_pago < $valor){
                            $commit = true;
                            $logDados['msg'] = "Valor pago não pode ser menor que o valor registrado";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $usuarioRepository = new UsuarioRepository();
                        $usuario = $usuarioRepository->findById($paypalCompra->getUsuarioId(), DbConexao::ROW_OBJECT);
                        if(empty($usuario)){
                            $logDados['msg'] = "Usuário não encontrado";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $logDados['usuario'] = $usuario->toArray();
                        $creditosAnterior = intval($usuario->getCreditos());
                        $creditosPosterior = $creditosAnterior + intval($paypalCompra->getCreditos());
                        $usuario->setCreditos($creditosPosterior)
                            ->setDataAtualizacao($data_atual);

                        $logDados['usuario_update'] = $usuario->toArray();
                        $update = $usuarioRepository->update($usuario);
                        if(!$update){
                            $logDados['msg'] = "Não foi possível atualizar créditos do usuário";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $logCreditoRepository = new LogCreditoRepository();
                        $logCredito = new TblLogCredito;
                        $logCredito->setUsuarioId($usuario->getId())
                            ->setPaypalCompraId($paypalCompra->getId())
                            ->setCreditoAnterior($creditosAnterior)
                            ->setCreditoPosterior($creditosPosterior)
                            ->setDataCriacao($data_atual);

                        $logDados['log_credito'] = $logCredito->toArray();
                        $insert = $logCreditoRepository->insert($logCredito);
                        if(!$insert){
                            $logDados['msg'] = "Não foi possível registrar log crédito";
                            return [
                                'success'=> false,
                                'msg' => $logDados['msg']
                            ];
                        }

                        $commit = true;
                        $logDados['success'] = true;
                        $logDados['msg'] = "Crédito adicionado com sucesso ao usuário";

                    }else{
                        $commit = true;
                        $logDados['success'] = true;
                        $logDados['msg'] = "Paypal compra atualizada para status {$response['status']}";
                    }
                    
                }else{
                    $logDados['success'] = true;
                    $logDados['msg'] = "Paypal compra já foi processada";
                }

                $result = [
                    'success' => $logDados['success'],
                    'msg' => $logDados['msg']
                ];

                if($logDados['success']){
                    $result['redirect'] = BASE_URL."/pages/pagamento.php?cod=".$cod;
                }

                return $result; 
            }catch(\Exception $e){
                $logDados['success'] = false;
                $logDados['msg'] = "Erro ao executar retorno do Paypal Order: ".$e->getMessage();
                $commit = false;
                return [
                    'success' => false,
                    'msg' => $logDados['msg']
                ];
            }finally{

                if($pdo->inTransaction()){
                    if($commit){
                        $pdo->commit();
                    }else{
                        $pdo->rollBack();
                    }
                }

                $pdo = NULL;
                $sucesso = ($logDados['success']) ? "S" : "N";
                $paypal_compra_id = (isset($logDados['paypal_compra'])) ? $logDados['paypal_compra']['id'] : NULL;
                $msg = $logDados['msg'];
                unset($logDados['success']);
                unset($logDados['msg']);

                $resultLogArquivo = ArquivoUtil::criarArquivoLogWebhook($logDados);
                $arquivo = ($resultLogArquivo['success']) ? $resultLogArquivo['arquivo'] : NULL;

                $logWebhook = new TblLogWebhook;
                $logWebhook->setMensagem($msg)
                    ->setSucesso($sucesso)
                    ->setPaypalCompraId($paypal_compra_id)
                    ->setArquivo($arquivo);

                $logWebhookRepository = new LogWebhookRepository();
                $logWebhookRepository->insert($logWebhook);
            }

        }

    }

?>