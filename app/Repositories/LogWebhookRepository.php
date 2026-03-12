<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblLogWebhook;

    class LogWebhookRepository extends DbConexao {

        private $tbl;

        public function __construct(){
            $this->tbl = "logs_webhook";
        }

        public function findById($id, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE id = :id LIMIT 1");
            $sql->bindParam(":id", $id, \PDO::PARAM_INT);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblLogWebhook;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function insert($log){

            $row = $log->toArray();
            if(empty($row['data_criacao'])){
                $row['data_criacao'] = strval(date('Y-m-d H:i:s'));
            }

            $sql = self::getPdo()->prepare("INSERT INTO {$this->tbl}(mensagem, sucesso, paypal_compra_id, arquivo, data_criacao)
            VALUES(:mensagem, :sucesso, :paypal_compra_id, :arquivo, :data_criacao)");
            $sql->bindParam(":mensagem", $row['mensagem'], \PDO::PARAM_STR);
            $sql->bindParam(":sucesso", $row['sucesso'], \PDO::PARAM_STR);
            $sql->bindParam(":paypal_compra_id", $row['paypal_compra_id'], \PDO::PARAM_INT);
            $sql->bindParam(":arquivo", $row['arquivo'], \PDO::PARAM_STR);
            $sql->bindParam(":data_criacao", $row['data_criacao'], \PDO::PARAM_STR);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

    }

?>