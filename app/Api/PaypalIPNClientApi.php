<?php

    namespace App\Api;

    class PaypalIPNClientApi {

        private $base_url;

        public function __construct(){
            if(PAYPAL_AMBIENTE == "sandbox"){
                $this->base_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
            }else if(PAYPAL_AMBIENTE == "production"){
                $this->base_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
            }else{
                throw new \Exception("Paypal IPN ambiente inválido");
            }
        }

        public function verify($post){
			
			$request = [
                'cmd' => "_notify-validate"
            ];
			foreach($post as $k => $value){
                $request[$k] = $value;
			}

            $headers = [
                "User-Agent: PHP-IPN-Verification-Script",
				"Connection: Close"
            ];

			$body = http_build_query($request);

			$ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->base_url,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_SSL_VERIFYPEER => 1,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_CAINFO => PAYPAL_CERTIFICADO,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_POST => 1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
            ]);

            $response = curl_exec($ch);
            $get_info = curl_getinfo($ch);
            curl_close($ch);

            $status_code = $get_info['http_code'];

            $success = ($status_code == 200 && $response == "VERIFIED");
            $msg = ($success) ? "Paypal IPN request válido" : "Paypal IPN request inválido";

            return [
                'success' => $success,
                'msg' => $msg,
                'request' => $request,
                'body' => $body,
                'response' => $response,
                'status_code' => $status_code
            ];
        }

    }

?>