<?php

    require_once("../vendor/autoload.php");

    use App\Repositories\LogWebhookRepository;
    use App\Database\DbConexao;
    use App\Utils\ArquivoUtil;

    function lerLogWebhook(){

        try {

            $log_id = (isset($_GET['log_id'])) ? filter_input(INPUT_GET, 'log_id', FILTER_SANITIZE_NUMBER_INT) : NULL;
            if(empty($log_id)){
                return [
                    'success' => false,
                    'msg' => "Log webhook não identificado"
                ];
            }

            $logWebhookRepository = new LogWebhookRepository();
            $logWebhook = $logWebhookRepository->findById($log_id, DbConexao::ROW_OBJECT);
            if(empty($logWebhook)){
                return [
                    'success' => false,
                    'msg' => "Log webhook não encontrado"
                ];
            }

            if(empty($logWebhook->getArquivo())){
                return [
                    'success' => false,
                    'msg' => "Log Webhook encontrado, mas sem arquivo registrado",
                    'dados' => $logWebhook->toArray()
                ];
            }

            $resultArquivo = ArquivoUtil::getArquivoLogWebhook($logWebhook->getArquivo());
            if(!$resultArquivo['success']){
                return [
                    'success' => false,
                    'msg' => $resultArquivo['msg'],
                    'dados' => $logWebhook->toArray()
                ];
            }

            return [
                'success' => true,
                'msg' => "Log Webhook encontrado com sucesso",
                'dados' => [
                    'log_webhook' => $logWebhook->toArray(),
                    'arquivo' => $resultArquivo['arquivo']
                ]
            ]; 
        }catch(\Exception $e){
            return [
                'success' => false,
                'msg' => "Erro ao ler arquivo de log webhook: ".$e->getMessage()
            ];
        }

    }

    $result = lerLogWebhook();
    echo "<pre>";
    print_r($result);
    echo "</pre>";

?>