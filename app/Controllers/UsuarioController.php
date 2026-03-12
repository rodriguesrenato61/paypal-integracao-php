<?php

    namespace App\Controllers;

    use App\Repositories\UsuarioRepository;
    use App\Database\DbConexao;

    class UsuarioController {

        public function getPage(){

            try {

                $repository = new UsuarioRepository();
                $usuario = $repository->findById(1, DbConexao::ROW_ARRAY);
                if(empty($usuario)){
                    return [
                        'success' => false,
                        'msg' => "Usuário não encontrado"
                    ];
                }

                $usuario['creditos_formatado'] = number_format(floatval($usuario['creditos']), 0, '.', ',');

                return [
                    'success' => true,
                    'msg' => "Página do usuário carregada com sucesso",
                    'dados' => $usuario
                ];
            }catch(\Exception $e){
                return [
                    'success' => false,
                    'msg' => "Erro ao carregar página do usuario: ".$e->getMessage()
                ];
            }

        }

    }

?>