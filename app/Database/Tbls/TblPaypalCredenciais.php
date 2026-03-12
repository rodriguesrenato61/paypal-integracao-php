<?php

    namespace App\Database\Tbls;

    class TblPaypalCredenciais {

        private $id;
        private $access_token;
        private $token_tipo;
        private $data_expiracao;
        private $ambiente;
        private $data_criacao;
        private $data_atualizacao;

        public function parse($row){
            $this->id = (isset($row['id'])) ? $row['id'] : NULL;
            $this->access_token = (isset($row['access_token'])) ? $row['access_token'] : NULL;
            $this->token_tipo = (isset($row['token_tipo'])) ? $row['token_tipo'] : NULL;
            $this->data_expiracao = (isset($row['data_expiracao'])) ? $row['data_expiracao'] : NULL;
            $this->ambiente = (isset($row['ambiente'])) ? $row['ambiente'] : NULL;
            $this->data_criacao = (isset($row['data_criacao'])) ? $row['data_criacao'] : NULL;
            $this->data_atualizacao = (isset($row['data_atualizacao'])) ? $row['data_atualizacao'] : NULL;
        }

        public function toArray(){
            return [
                'id' => $this->id,
                'access_token' => $this->access_token,
                'token_tipo' => $this->token_tipo,
                'data_expiracao' => $this->data_expiracao,
                'ambiente' => $this->ambiente,
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

        public function setAccessToken($access_token){
            $this->access_token = $access_token;
            return $this;
        }

        public function getAccessToken(){
            return $this->access_token;
        }

        public function setTokenTipo($token_tipo){
            $this->token_tipo = $token_tipo;
            return $this;
        }

        public function getTokenTipo(){
            return $this->token_tipo;
        }

        public function setDataExpiracao($data_expiracao){
            $this->data_expiracao = $data_expiracao;
            return $this;
        }

        public function getDataExpiracao(){
            return $this->data_expiracao;
        }

        public function setAmbiente($ambiente){
            $this->ambiente = $ambiente;
            return $this;
        }

        public function getAmbiente(){
            return $this->ambiente;
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