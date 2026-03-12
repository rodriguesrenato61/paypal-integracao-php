<?php

    namespace App\Api;

    class PaypalOrderClientApi {

        private $base_url;
        private $client_id;
        private $client_secret;
        private $token_tipo;
        private $access_token;

        public function __construct(){
            if(PAYPAL_AMBIENTE == "sandbox"){
                $this->base_url = "https://api-m.sandbox.paypal.com";
                $this->client_id = PAYPAL_CLIENT_ID_SANDBOX;
                $this->client_secret = PAYPAL_CLIENT_SECRET_SANDBOX;
            }else if(PAYPAL_AMBIENTE == "production"){
                $this->base_url = "https://api-m.paypal.com";
                $this->client_id = PAYPAL_CLIENT_ID_PRODUCTION;
                $this->client_secret = PAYPAL_CLIENT_SECRET_PRODUCTION;
            }else{
                throw new \Exception("Paypal Payment ambiente inválido");
            }
        }

        public function setTokenTipo($token_tipo){
            $this->token_tipo = $token_tipo;
            return $this;
        }

        public function getTokenTipo(){
            return $this->token_tipo;
        }

        public function setAccessToken($access_token){
            $this->access_token = $access_token;
            return $this;
        }

        public function getAccessToken(){
            return $this->access_token;
        }

        private function extrairMsgErro($msg, $response){

            $errors = [];
            if(isset($response['debug_id'])){
                $errors[] = "Debug Id {$response['debug_id']}";
            }
            if(isset($response['name'])){
                $errors[] = $response['name'];
            }
            if(isset($response['message'])){
                $errors[] = $response['message'];
            }
            if(isset($response['details'])){
                foreach($response['details'] as $detail){
                    if(isset($detail['field']) && isset($detail['description']) && isset($detail['location'])){
                        $errors[] = $detail['field']." - {$detail['dscription']} - {$detail['body']}/";
                    }
                }
            }

            foreach($errors as $k => $error){
                if($k == 0){
                    $msg .= ": ";
                }else{
                    $msg .= " - ";
                }
                $msg .= $error;
            }

            return $msg;
        }

        public function autenticar(){

            $endpoint = $this->base_url."/v1/oauth2/token";

            $headers = [
                "Authorization: Basic ".base64_encode("{$this->client_id}:{$this->client_secret}"),
                "Content-Type: application/x-www-form-urlencoded",
                "Accept: application/json"
            ];

            $request = [
                'grant_type' => "client_credentials"
            ];

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($request),
                CURLOPT_HTTPHEADER => $headers,
            ));
            
            $response = [];
            $response['text'] = curl_exec($ch);
            $get_info = curl_getinfo($ch);
            curl_close($ch);

            $response['json'] = json_decode($response['text'], true);
            $status_code = $get_info['http_code'];

            $success = ($status_code == 200);
            $msg = ($success) ? "Access token Paypal Payment gerado com sucesso!" : "Erro ao gerar access token Paypal Payment!";

            return [
                'success' => $success,
                'msg' => $msg,
                'response' => $response,
                'status_code' => $status_code
            ];
        }

        public function createOrder($request){

            $endpoint = $this->base_url."/v2/checkout/orders";

            $headers = [
                "Authorization: {$this->token_tipo} ".$this->access_token,
                "Content-Type: application/json",
                /*"Paypal-Request-Id: "*/
            ];

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request),
                CURLOPT_HTTPHEADER => $headers,
            ));
            
            $response = [];
            $response['text'] = curl_exec($ch);
            $get_info = curl_getinfo($ch);
            curl_close($ch);

            $response['json'] = json_decode($response['text'], true);
            $status_code = $get_info['http_code'];

            $success = ($status_code == 200);
            $msg = ($success) ? "Paypal Order criado com sucesso!" : $this->extrairMsgErro("Erro ao criar Paypal Order", $response['json']);

            return [
                'success' => $success,
                'msg' => $msg,
                'response' => $response,
                'status_code' => $status_code
            ];
        }

        public function findOrder($order_id){

            $endpoint = $this->base_url."/v2/checkout/orders/".$order_id;

            $headers = [
                "Authorization: {$this->token_tipo} ".$this->access_token,
                "Accept: application/json"
            ];

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $headers,
            ));
            
            $response = [];
            $response['text'] = curl_exec($ch);
            $get_info = curl_getinfo($ch);
            curl_close($ch);

            $response['json'] = json_decode($response['text'], true);
            $status_code = $get_info['http_code'];

            $success = ($status_code == 200);
            $msg = ($success) ? "Paypal Order consultado com sucesso!" : $this->extrairMsgErro("Erro ao consultar Paypal Order", $response['json']);

            return [
                'success' => $success,
                'msg' => $msg,
                'response' => $response,
                'status_code' => $status_code
            ];
        }

    }

?>