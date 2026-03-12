<?php

    namespace App\Utils;

    class ArquivoUtil {

        public static function criarArquivoLogWebhook($dados){

            $nome_arquivo = "log_webhook_".time()."_".rand(0, 1000).".json"; 
			$arquivo = fopen(LOGS_WEBHOOK_DIR."/".$nome_arquivo,'w'); 
			if ($arquivo === false){
				return [
					'success' => false,
					'msg' => "Não foi possível criar o arquivo de log {$nome_arquivo}"
				];
			}
            
            $json = json_encode($dados);
            fwrite($arquivo, $json);  
            fclose($arquivo);
            
            return [
                'success' => true,
                'msg' => "Arquivo de log criado com sucesso!",
                'arquivo' => $nome_arquivo
            ];
        }

        public static function getArquivoLogWebhook($nome_arquivo){

            $logArquivo = file_get_contents(LOGS_WEBHOOK_DIR."/".$nome_arquivo);
            if($logArquivo === false){
                return [
                    'success' => false,
                    'msg' => "Não foi possível ler o arquivo de log {$nome_arquivo}"
                ];
            }

            return [
                'success' => true,
                'msg' => "Arquivo de log encontrado com sucesso",
                'arquivo' => json_decode($logArquivo, true)
            ];
        }
			  
    }

?>