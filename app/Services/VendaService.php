<?php

    namespace App\Services;

    use App\Database\DbConexao;
    use App\Repositories\UsuarioRepository;
    use App\Repositories\PacoteRepository;
    use App\Database\Tbls\TblPaypalCompra;
    use App\Repositories\PaypalCompraRepository;

    class VendaService {

        public function comprar($form){

            $usuarioRepository = new UsuarioRepository();
            $usuario = $usuarioRepository->findByUsername($form['username'], DbConexao::ROW_OBJECT);
            if(empty($usuario)){
                return [
                    'success' => false,
                    'msg' => "Usuário {$form['username']} não encontrado"
                ];
            }

            if($form['email'] != $usuario->getEmail()){
                return [
                    'success' => false,
                    'msg' => "Email não corresponde ao registrado"
                ];
            }

            $pacoteRepository = new PacoteRepository();
            $pacote = $pacoteRepository->findById($form['pacote_id'], DbConexao::ROW_OBJECT);
            if(empty($pacote)){
                return [
                    'success' => false,
                    'msg' => "Pacote não encontrado"
                ];
            }

            $paypalCompra = new TblPaypalCompra;
            $paypalCompra->setTipo($form['tipo'])
                ->setUsuarioId($usuario->getId())
                ->setPacoteId($pacote->getId())
                ->setDescricao($pacote->getNome())
                ->setCreditos($pacote->getCreditos())
                ->setCurrencyCode(PAYPAL_CURRENCY_CODE)
                ->setValor($pacote->getValor())
                ->setValorPago(0.00)
                ->setPaymentStatus("New")
                ->setAmbiente(PAYPAL_AMBIENTE)
                ->setCodigoReferencia(DbConexao::geraCodigoInsercao());

            $paypalCompraRepository = new PaypalCompraRepository();
            $insert = $paypalCompraRepository->insert($paypalCompra);
            if(!$insert){
                return [
                    'success' => false,
                    'msg' => "Não foi possível registrar Paypal compra"
                ];
            }

            $paypalCompra = $paypalCompraRepository->findByCodigoReferencia($paypalCompra->getCodigoReferencia(), DbConexao::ROW_OBJECT);
            if(empty($paypalCompra)){
                return [
                    'success' => false,
                    'msg' => "Paypal Compra recém criado não encontrado"
                ];
            }

            $paypalService = new PaypalService;

            if($form['tipo'] == TblPaypalCompra::TIPO_IPN){
                $result = $paypalService->createFormIPN($paypalCompra);     
            }else if($form['tipo'] == TblPaypalCompra::TIPO_ORDER){
                $result = $paypalService->setComprador($usuario->getUsername())
                    ->createOrder($paypalCompra);
            }else{
                throw new \Exception("Tipo {$form['tipo']} Paypal Compra inválido");
            }

            return $result;
        }

    }

?>