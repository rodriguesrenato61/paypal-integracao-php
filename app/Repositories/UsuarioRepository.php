<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblUsuario;

    class UsuarioRepository extends DbConexao {

        private $tbl;

        public function __construct(){
            $this->tbl = "usuarios";
        }

        public function findById($id, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE id = :id LIMIT 1");
            $sql->bindParam(":id", $id, \PDO::PARAM_INT);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblUsuario;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function findByUsername($username, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE username = :username LIMIT 1");
            $sql->bindParam(":username", $username, \PDO::PARAM_STR);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblUsuario;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

        public function update($row){

            $row = $row->toArray();
            if(empty($row['data_atualizacao'])){
                $row['data_atualizacao'] = strval(date('Y-m-d H:i:s'));
            }
            
            $sql = self::getPdo()->prepare("UPDATE {$this->tbl} SET username = :username, email = :email, nome = :nome, profissao = :profissao, creditos = :creditos, data_atualizacao = :data_atualizacao WHERE id = :id");
            $sql->bindParam(":username", $row['username'], \PDO::PARAM_STR);
            $sql->bindParam(":email", $row['email'], \PDO::PARAM_STR);
            $sql->bindParam(":nome", $row['nome'], \PDO::PARAM_STR);
            $sql->bindParam(":profissao", $row['profissao'], \PDO::PARAM_STR);
            $sql->bindParam(":creditos", $row['creditos'], \PDO::PARAM_INT);
            $sql->bindParam(":data_atualizacao", $row['atualizacao'], \PDO::PARAM_STR);
            $sql->bindParam(":id", $row['id'], \PDO::PARAM_INT);
            $sql->execute();

            return ($sql->rowCount() > 0);
        }

    }

?>