<?php

    namespace App\Database\Tbls;

    class TblLogWebhook {

        private $id;
        private $mensagem;
        private $sucesso;
        private $paypal_compra_id;
        private $arquivo;
        private $data_criacao;

        public function parse($row){
            $this->id = (isset($row['id'])) ? $row['id'] : NULL;
            $this->mensagem = (isset($row['mensagem'])) ? $row['mensagem'] : NULL;
            $this->sucesso = (isset($row['sucesso'])) ? $row['sucesso'] : NULL;
            $this->paypal_compra_id = (isset($row['paypal_compra_id'])) ? $row['paypal_compra_id'] : NULL;
            $this->arquivo = (isset($row['arquivo'])) ? $row['arquivo'] : NULL;
            $this->data_criacao = (isset($row['data_criacao'])) ? $row['data_criacao'] : NULL;
        }

        public function toArray(){
            return [
                'id' => $this->id,
                'mensagem' => $this->mensagem,
                'sucesso' => $this->sucesso,
                'paypal_compra_id' => $this->paypal_compra_id,
                'arquivo' => $this->arquivo,
                'data_criacao' => $this->data_criacao
            ];
        }

        public function setId($id){
            $this->id = $id;
            return $this;
        }

        public function getId(){
            return $this;
        }

        public function setMensagem($mensagem){
            $this->mensagem = $mensagem;
            return $this;
        }

        public function getMensagem(){
            return $this->mensagem;
        }

        public function setSucesso($sucesso){
            $this->sucesso = $sucesso;
            return $this;
        }

        public function getSucesso(){
            return $this->sucesso;
        }

        public function setPaypalCompraId($paypal_compra_id){
            $this->paypal_compra_id = $paypal_compra_id;
            return $this;
        }

        public function getPaypalCompraId(){
            return $this->paypal_compra_id;
        }

        public function setArquivo($arquivo){
            $this->arquivo = $arquivo;
            return $this;
        }

        public function getArquivo(){
            return $this->arquivo;
        }

        public function setDataCriacao($data_criacao){
            $this->data_criacao = $data_criacao;
            return $this;
        }

        public function getDataCriacao(){
            return $this->data_criacao;
        }

    }

?>