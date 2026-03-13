<?php

    namespace App\Controllers;

    use App\Services\PaypalService;

    class PaypalController {

        public function webhookIPN(){

            try {

                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    $service = new PaypalService;
                    return $service->webhookIPN($_POST);
                }else{
                    return [
                        'success' => false,
                        'msg' => "Requisição inválida"
                    ];
                }

            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao processar webhook: ".$e->getMessage()
                ];
            }

        }

        public function retornoOrder(){

            try {

                $request = [
                    'get' => (isset($_GET)) ? $_GET : NULL
                ];

                $service = new PaypalService;
                $result = $service->retornoOrder($request);

                if(!$result['success']){
                    echo "<h4>{$result['msg']}</h3>";
                }
                
                if(isset($result['redirect'])){
                    header("Location: {$result['redirect']}");
                }
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao processar retorno do Paypal Payment: ".$e->getMessage()
                ];
            }

        }

    }

?>