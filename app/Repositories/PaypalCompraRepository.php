<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblPaypalCompra;

    class PaypalCompraRepository extends DbConexao {

        private $tbl;
        private $vw;

        public function __construct(){
            $this->tbl = "paypal_compras";
            $this->vw = "vw_paypal_compras";
        }

        public function findById($id, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE id = :id LIMIT 1");
            $sql->bindParam(":id", $id, \PDO::PARAM_INT);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblPaypalCompra;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function findByCodigoReferencia($codigo, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE codigo_referencia = :codigo LIMIT 1");
            $sql->bindParam(":codigo", $codigo, \PDO::PARAM_STR);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblPaypalCompra;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function findByViewCodigoReferencia($codigo){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->vw} WHERE codigo_referencia = :codigo LIMIT 1");
            $sql->bindParam(":codigo", $codigo, \PDO::PARAM_STR);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = $this->criaRow($registro);
            }

            return $row;
        }

        private function criaRow($registro){

            $row = new TblPaypalCompra;
            $row->parse($registro);
            $row = $row->toArray();

            if(isset($registro['username'])){
                $row['username'] = $registro['username'];
            }
            if(isset($registro['usuario_nome'])){
                $row['usuario_nome'] = $registro['usuario_nome'];
            }
            if(isset($registro['data_criacao_br'])){
                $row['data_criacao_br'] = $registro['data_criacao_br'];
            }
            if(isset($registro['data_atualizacao_br'])){
                $row['data_atualizacao_br'] = $registro['data_atualizacao_br'];
            }

            return $row;
        }

        public function insert($paypal_compra){

            $row = $paypal_compra->toArray();
            if(empty($row['data_criacao'])){
                $row['data_criacao'] = strval(date('Y-m-d H:i:s'));
            }

            $sql = self::getPdo()->prepare("INSERT INTO {$this->tbl}(tipo, usuario_id, pacote_id, descricao, creditos, currency_code, valor, valor_pago, payment_status, invoice_id, txn_id, order_id, ambiente, codigo_referencia, data_criacao)
            VALUES(:tipo, :usuario_id, :pacote_id, :descricao, :creditos, :currency_code, :valor, :valor_pago, :payment_status, :invoice_id, :txn_id, :order_id, :ambiente, :codigo_referencia, :data_criacao)");
            $sql->bindParam(":tipo", $row['tipo'], \PDO::PARAM_STR);
            $sql->bindParam(":usuario_id", $row['usuario_id'], \PDO::PARAM_INT);
            $sql->bindParam(":pacote_id", $row['pacote_id'], \PDO::PARAM_INT);
            $sql->bindParam(":descricao", $row['descricao'], \PDO::PARAM_STR);
            $sql->bindParam(":creditos", $row['creditos'], \PDO::PARAM_INT);
            $sql->bindParam(":currency_code", $row['currency_code'], \PDO::PARAM_STR);
            $sql->bindParam(":valor", $row['valor'], \PDO::PARAM_STR);
            $sql->bindParam(":valor_pago", $row['valor_pago'], \PDO::PARAM_STR);
            $sql->bindParam(":payment_status", $row['payment_status'], \PDO::PARAM_STR);
            $sql->bindParam(":invoice_id", $row['invoice_id'], \PDO::PARAM_STR);
            $sql->bindParam(":txn_id", $row['txn_id'], \PDO::PARAM_STR);
            $sql->bindParam(":order_id", $row['order_id'], \PDO::PARAM_STR);
            $sql->bindParam(":ambiente", $row['ambiente'], \PDO::PARAM_STR);
            $sql->bindParam(":codigo_referencia", $row['codigo_referencia'], \PDO::PARAM_STR);
            $sql->bindParam(":data_criacao", $row['data_criacao'], \PDO::PARAM_STR);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

        public function update($paypal_compra){

            $row = $paypal_compra->toArray();
            if(empty($row['data_atualizacao'])){
                $row['data_atualizacao'] = strval(date('Y-m-d H:i:s'));
            }

            $sql = self::getPdo()->prepare("UPDATE {$this->tbl} SET tipo = :tipo, usuario_id = :usuario_id, pacote_id = :pacote_id, descricao = :descricao, creditos = :creditos, currency_code = :currency_code, valor = :valor, valor_pago = :valor_pago, payment_status = :payment_status, invoice_id = :invoice_id, txn_id = :txn_id, order_id = :order_id, ambiente = :ambiente, codigo_referencia = :codigo_referencia, data_atualizacao = :data_atualizacao WHERE id = :id");
            $sql->bindParam(":tipo", $row['tipo'], \PDO::PARAM_STR);
            $sql->bindParam(":usuario_id", $row['usuario_id'], \PDO::PARAM_INT);
            $sql->bindParam(":pacote_id", $row['pacote_id'], \PDO::PARAM_INT);
            $sql->bindParam(":descricao", $row['descricao'], \PDO::PARAM_STR);
            $sql->bindParam(":creditos", $row['creditos'], \PDO::PARAM_INT);
            $sql->bindParam(":currency_code", $row['currency_code'], \PDO::PARAM_STR);
            $sql->bindParam(":valor", $row['valor'], \PDO::PARAM_STR);
            $sql->bindParam(":valor_pago", $row['valor_pago'], \PDO::PARAM_STR);
            $sql->bindParam(":payment_status", $row['payment_status'], \PDO::PARAM_STR);
            $sql->bindParam(":invoice_id", $row['invoice_id'], \PDO::PARAM_STR);
            $sql->bindParam(":txn_id", $row['txn_id'], \PDO::PARAM_STR);
            $sql->bindParam(":order_id", $row['order_id'], \PDO::PARAM_STR);
            $sql->bindParam(":ambiente", $row['ambiente'], \PDO::PARAM_STR);
            $sql->bindParam(":codigo_referencia", $row['codigo_referencia'], \PDO::PARAM_STR);
            $sql->bindParam(":data_atualizacao", $row['data_atualizacao'], \PDO::PARAM_STR);
            $sql->bindParam(":id", $row['id'], \PDO::PARAM_INT);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

    }

?>