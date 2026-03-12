<?php

    namespace App\Controllers;

    use App\Database\DbConexao;
    use App\Repositories\PacoteRepository;

    class PacoteController {

        public function get(){

            try {

                $pacote_id = (isset($_GET['pacote_id'])) ? filter_input(INPUT_GET, 'pacote_id', FILTER_SANITIZE_NUMBER_INT) : NULL;
                if(empty($pacote_id)){
                    return [
                        'success' => false,
                        'msg' => "Pacote não identificado"
                    ];
                }

                $repository = new PacoteRepository();
                $pacote = $repository->findById($pacote_id, DbConexao::ROW_ARRAY);
                if(empty($pacote)){
                    return [
                        'success' => false,
                        'msg' => "Pacote não encontrado"
                    ];
                }

                $pacote['valor_formatado'] = "R$ ".number_format(floatval($pacote['valor']), 2, ',', '.');
                $pacote['creditos_formatado'] = number_format(floatval($pacote['creditos']), 2, ',', '.');

                return [
                    'success' => true,
                    'msg' => "Pacote encontrado com sucesso",
                    'dados' => $pacote
                ];
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao consultar pacote: ".$e->getMessage()
                ];
            }

        }

    }

?>