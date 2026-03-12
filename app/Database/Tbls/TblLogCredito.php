<?php

    namespace App\Database\Tbls;

    class TblLogCredito {

        private $id;
        private $usuario_id;
        private $paypal_compra_id;
        private $credito_anterior;
        private $credito_posterior;
        private $data_criacao;

        public function parse($row){
            $this->id = (isset($row['id'])) ? $row['id'] : NULL;
            $this->usuario_id = (isset($row['usuario_id'])) ? $row['usuario_id'] : NULL;
            $this->paypal_compra_id = (isset($row['paypal_compra_id'])) ? $row['paypal_compra_id'] : NULL;
            $this->credito_anterior = (isset($row['credito_anterior'])) ? $row['credito_anterior'] : NULL;
            $this->credito_posterior = (isset($row['credito_posterior'])) ? $row['credito_posterior'] : NULL;
            $this->data_criacao = (isset($row['data_criacao'])) ? $row['data_criacao'] : NULL;
        }

        public function toArray(){
            return [
                'id' => $this->id,
                'usuario_id' => $this->usuario_id,
                'paypal_compra_id' => $this->paypal_compra_id,
                'credito_anterior' => $this->credito_anterior,
                'credito_posterior' => $this->credito_posterior,
                'data_criacao' => $this->data_criacao
            ];
        }

        public function setId($id){
            $this->id = $id;
            return $this;
        }

        public function getId(){
            return $this->id;
        }

        public function setUsuarioId($usuario_id){
            $this->usuario_id = $usuario_id;
            return $this;
        }

        public function getUsuarioId(){
            return $this->usuario_id;
        }

        public function setPaypalCompraId($paypal_compra_id){
            $this->paypal_compra_id = $paypal_compra_id;
            return $this;
        }

        public function getPaypalCompraId(){
            return $this->paypal_compra_id;
        }

        public function setCreditoAnterior($credito_anterior){
            $this->credito_anterior = $credito_anterior;
            return $this;
        }

        public function getCreditoAnterior(){
            return $this->credito_anterior;
        }

        public function setCreditoPosterior($credito_posterior){
            $this->credito_posterior = $credito_posterior;
            return $this;
        }

        public function getCreditoPosterior(){
            return $this->credito_posterior;
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