<?php

    require_once("../vendor/autoload.php");


    function testeWebhook(){

        try {

            $nome = (isset($_GET['nome'])) ? filter_input(INPUT_GET, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) : NULL;
            if(empty($nome)){
                return [
                    'success' => false,
                    'msg' => "Nome do arquivo de request não identificado"
                ];
            }

            $endpoint = BASE_URL."/processos/webhook-ipn.php";
            $request = file_get_contents(REQUESTS_DIR."/".$nome);
            if($request === false){
                return [
                    'success' => false,
                    'msg' => "Arquivo {$nome} não encontrado"
                ];
            }

            $request = json_decode($request, true);
            $body = http_build_query($request);

            $ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => $endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body
			]);

            $response = [];
            $response['text'] = curl_exec($ch);
            $get_info = curl_getinfo($ch);
            curl_close($ch);

            $response['json'] = json_decode($response['text'], true);
            $status_code = $get_info['http_code'];

            return [
                'success' => true,
                'msg' => "Webhook Paypal IPN testado",
                'status_code' => $status_code,
                'dados' => [
                    'arquivo' => $nome,
                    'request' => $request,
                    'body' => $body,
                    'response' => $response
                ]
            ];

        }catch(\Exception $e){
            return [
                'success' => false,
                'msg' => "Erro ao testar Webhook Paypal IPN: ".$e->getMessage()
            ];
        }

    }

    $result = testeWebhook();
    echo "<pre>";
    print_r($result);
    echo "</pre>";

?>