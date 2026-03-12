<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblPaypalCredenciais;

    class PaypalCredenciaisRepository extends DbConexao {

        private $tbl;

        public function __construct(){
            $this->tbl = "paypal_credenciais";
        }

        public function get($tipo_row){

            $row = NULL;
            $ambiente = PAYPAL_AMBIENTE;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE ambiente = :ambiente LIMIT 1");
            $sql->bindParam(":ambiente", $ambiente, \PDO::PARAM_STR);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblPaypalCredenciais;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function update($credenciais){

            $row = $credenciais->toArray();
            if(empty($row['data_atualizacao'])){
                $row['data_atualizacao'] = strval(date('Y-m-d H:i:s'));
            }

            $sql = self::getPdo()->prepare("UPDATE {$this->tbl} SET access_token = :access_token, token_tipo = :token_tipo, data_expiracao = :data_expiracao, ambiente = :ambiente, data_atualizacao = :data_atualizacao WHERE id = :id");
            $sql->bindParam(":access_token", $row['access_token'], \PDO::PARAM_STR);
            $sql->bindParam(":token_tipo", $row['token_tipo'], \PDO::PARAM_STR);
            $sql->bindParam(":data_expiracao", $row['data_expiracao'], \PDO::PARAM_STR);
            $sql->bindParam(":ambiente", $row['ambiente'], \PDO::PARAM_STR);
            $sql->bindParam(":data_atualizacao", $row['data_atualizacao'], \PDO::PARAM_STR);
            $sql->bindParam(":id", $row['id'], \PDO::PARAM_INT);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

    }

?>