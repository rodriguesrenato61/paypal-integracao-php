<?php

    namespace App\Repositories;

    use App\Database\DbConexao;
    use App\Database\Tbls\TblPacote;

    class PacoteRepository extends DbConexao {

        private $tbl;

        public function __construct(){
            $this->tbl = "pacotes";
        }

        public function findAll($tipo_row){

            $rows = [];

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl}");
            $sql->execute();

            if($sql->rowCount() > 0){
                while($registro = $sql->fetch()){
                    $row = new TblPacote;
                    $row->parse($registro);
                    if($tipo_row == DbConexao::ROW_OBJECT){
                        $rows[] = $row;
                    }else if($tipo_row == DbConexao::ROW_ARRAY){
                        $rows[] = $row->toArray();
                    }
                }
            }

            return $rows;
        }

        public function findById($id, $tipo_row){

            $row = NULL;

            $sql = self::getPdo()->prepare("SELECT * FROM {$this->tbl} WHERE id = :id LIMIT 1");
            $sql->bindParam(":id", $id, \PDO::PARAM_INT);
            $sql->execute();

            if($sql->rowCount() > 0){
                $registro = $sql->fetch();
                $row = new TblPacote;
                $row->parse($registro);
                if($tipo_row == DbConexao::ROW_ARRAY){
                    $row = $row->toArray();
                }
            }

            return $row;
        }

    }

?>