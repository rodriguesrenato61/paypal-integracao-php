<?php

    namespace App\Database\Tbls;

    class TblPacote {

        private $id;
        private $nome;
        private $valor;
        private $creditos;
        private $data_criacao;
        private $data_atualizacao;

        public function parse($row){
            $this->id = (isset($row['id'])) ? $row['id'] : NULL;
            $this->nome = (isset($row['nome'])) ? $row['nome'] : NULL;
            $this->valor = (isset($row['valor'])) ? $row['valor'] : NULL;
            $this->creditos = (isset($row['creditos'])) ? $row['creditos'] : NULL;
            $this->data_criacao = (isset($row['data_criacao'])) ? $row['data_criacao'] : NULL;
            $this->data_atualizacao = (isset($row['data_atualizacao'])) ? $row['data_atualizacao'] : NULL;
        }

        public function toArray(){
            return [
                'id' => $this->id,
                'nome' => $this->nome,
                'valor' => $this->valor,
                'creditos' => $this->creditos,
                'data_criacao' => $this->data_criacao,
                'data_atualizacao' => $this->data_atualizacao
            ];
        }

        public function setId($id){
            $this->id = $id;
            return $this;
        }

        public function getId(){
            return $this->id;
        }

        public function setNome($nome){
            $this->nome = $nome;
            return $this;
        }

        public function getNome(){
            return $this->nome;
        }

        public function setValor($valor){
            $this->valor = $valor;
            return $this;
        }

        public function getValor(){
            return $this->valor;
        }

        public function setCreditos($creditos){
            $this->creditos = $creditos;
            return $this;
        }

        public function getCreditos(){
            return $this->creditos;
        }

        public function setDataCriacao($data_criacao){
            $this->data_criacao = $data_criacao;
            return $this;
        }

        public function getDataCriacao(){
            return $this->data_criacao;
        }

        public function setDataAtualizacao($data_atualizacao){
            $this->data_atualizacao = $data_atualizacao;
            return $this;
        }

        public function getDataAtualizacao(){
            return $this->data_atualizacao;
        }

    }

?>