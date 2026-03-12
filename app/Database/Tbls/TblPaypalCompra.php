<?php

    namespace App\Database\Tbls;

    class TblPaypalCompra {

        const TIPO_IPN = "ipn";
        const TIPO_ORDER = "order";
        const AMBIENTE_SANDBOX = "sandbox";
        const AMBIENTE_PRODUCTION = "production";

        private $id;
        private $tipo;
        private $usuario_id;
        private $pacote_id;
        private $descricao;
        private $creditos;
        private $currency_code;
        private $valor;
        private $valor_pago;
        private $payment_status;
        private $invoice_id;
        private $txn_id;
        private $order_id;
        private $ambiente;
        private $codigo_referencia;
        private $data_criacao;
        private $data_atualizacao;

        public function parse($row){
            $this->id = (isset($row['id'])) ? $row['id'] : NULL;
            $this->tipo = (isset($row['tipo'])) ? $row['tipo'] : NULL;
            $this->usuario_id = (isset($row['usuario_id'])) ? $row['usuario_id'] : NULL;
            $this->pacote_id = (isset($row['pacote_id'])) ? $row['pacote_id'] : NULL;
            $this->descricao = (isset($row['descricao'])) ? $row['descricao'] : NULL;
            $this->creditos = (isset($row['creditos'])) ? $row['creditos'] : NULL;
            $this->currency_code = (isset($row['currency_code'])) ? $row['currency_code'] : NULL;
            $this->valor = (isset($row['valor'])) ? $row['valor'] : NULL;
            $this->valor_pago = (isset($row['valor_pago'])) ? $row['valor_pago'] : NULL;
            $this->payment_status = (isset($row['payment_status'])) ? $row['payment_status'] : NULL;
            $this->invoice_id = (isset($row['invoice_id'])) ? $row['invoice_id'] : NULL;
            $this->txn_id = (isset($row['txn_id'])) ? $row['txn_id'] : NULL;
            $this->order_id = (isset($row['order_id'])) ? $row['order_id'] : NULL;
            $this->ambiente = (isset($row['ambiente'])) ? $row['ambiente'] : NULL;
            $this->codigo_referencia = (isset($row['codigo_referencia'])) ? $row['codigo_referencia'] : NULL;
            $this->data_criacao = (isset($row['data_criacao'])) ? $row['data_criacao'] : NULL;
            $this->data_atualizacao = (isset($row['data_atualizacao'])) ? $row['data_atualizacao'] : NULL;
        }

        public function toArray(){
            return [
                'id' => $this->id,
                'tipo' => $this->tipo,
                'usuario_id' => $this->usuario_id,
                'pacote_id' => $this->pacote_id,
                'descricao' => $this->descricao,
                'creditos' => $this->creditos,
                'currency_code' => $this->currency_code,
                'valor' => $this->valor,
                'valor_pago' => $this->valor_pago,
                'payment_status' => $this->payment_status,
                'invoice_id' => $this->invoice_id,
                'txn_id' => $this->txn_id,
                'order_id' => $this->order_id,
                'ambiente' => $this->ambiente,
                'codigo_referencia' => $this->codigo_referencia,
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

        public function setTipo($tipo){
            $this->tipo = $tipo;
            return $this;
        }

        public function getTipo(){
            return $this->tipo;
        }

        public function setUsuarioId($usuario_id){
            $this->usuario_id = $usuario_id;
            return $this;
        }

        public function getUsuarioId(){
            return $this->usuario_id;
        }

        public function setPacoteId($pacote_id){
            $this->pacote_id = $pacote_id;
            return $this;
        }

        public function getPacoteId(){
            return $this->pacote_id;
        }

        public function setDescricao($descricao){
            $this->descricao = $descricao;
            return $this;
        }

        public function getDescricao(){
            return $this->descricao;
        }

        public function setCreditos($creditos){
            $this->creditos = $creditos;
            return $this;
        }

        public function getCreditos(){
            return $this->creditos;
        }

        public function setCurrencyCode($currency_code){
            $this->currency_code = $currency_code;
            return $this;
        }

        public function getCurrencyCode(){
            return $this->currency_code;
        }

        public function setValor($valor){
            $this->valor = $valor;
            return $this;
        }

        public function getValor(){
            return $this->valor;
        }

        public function setValorPago($valor_pago){
            $this->valor_pago = $valor_pago;
            return $this;
        }

        public function getValorPago(){
            return $this->valor_pago;
        }

        public function setPaymentStatus($payment_status){
            $this->payment_status = $payment_status;
            return $this;
        }

        public function getPaymentStatus(){
            return $this->payment_status;
        }

        public function setInvoiceId($invoice_id){
            $this->invoice_id = $invoice_id;
            return $this;
        }

        public function getInvoiceId(){
            return $this->invoice_id;
        }

        public function setTxnId($txn_id){
            $this->txn_id = $txn_id;
            return $this;
        }

        public function getTxnId(){
            return $this->txn_id;
        }

        public function setOrderId($order_id){
            $this->order_id = $order_id;
            return $this;
        }

        public function getOrderId(){
            return $this->order_id;
        }

        public function setAmbiente($ambiente){
            $this->ambiente = $ambiente;
            return $this;
        }

        public function getAmbiente(){
            return $this->ambiente;
        }

        public function setCodigoReferencia($codigo_referencia){
            $this->codigo_referencia = $codigo_referencia;
            return $this;
        }

        public function getCodigoReferencia(){
            return $this->codigo_referencia;
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