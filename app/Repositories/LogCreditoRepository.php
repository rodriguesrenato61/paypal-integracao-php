<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblLogCredito;

    class LogCreditoRepository extends DbConexao {

        private $tbl;

        public function __construct(){
            $this->tbl = "logs_creditos";
        }

        public function insert($log){

            $row = $log->toArray();
            if(empty($row['data_criacao'])){
                $row['data_criacao'] = strval(date('Y-m-d H:i:s'));
            }

            $sql = self::getPdo()->prepare("INSERT INTO {$this->tbl}(usuario_id, paypal_compra_id, credito_anterior, credito_posterior, data_criacao)
            VALUES(:usuario_id, :paypal_compra_id, :credito_anterior, :credito_posterior, :data_criacao)");
            $sql->bindParam(":usuario_id", $row['usuario_id'], \PDO::PARAM_INT);
            $sql->bindParam(":paypal_compra_id", $row['paypal_compra_id'], \PDO::PARAM_INT);
            $sql->bindParam(":credito_anterior", $row['credito_anterior'], \PDO::PARAM_INT);
            $sql->bindParam(":credito_posterior", $row['credito_posterior'], \PDO::PARAM_INT);
            $sql->bindParam(":data_criacao", $row['data_criacao'], \PDO::PARAM_STR);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

    }

?>