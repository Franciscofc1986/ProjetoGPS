<?php

include_once realpath(__DIR__) . '/../model/service/ServiceLocator.php';
include_once realpath(__DIR__) . '/../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../model/entity/Rastreador.php';
include_once realpath(__DIR__) . './ServidorWSBase.php';
include_once realpath(__DIR__) . './ClienteWSRastreador.php';
include_once realpath(__DIR__) . './ClienteWSControllerRastreador.php';
include_once realpath(__DIR__) . './TipoCliente.php';
include_once realpath(__DIR__) . './TipoComunicacao.php';
include_once realpath(__DIR__) . './Servicos.php';

class ServidorWSRastreador extends ServidorWSBase {

    public function __construct($ip = 'localhost', $porta = 5432, $printarEventos = false) {
        parent::__construct($ip, $porta, $printarEventos);
        $this->clienteWSController = new ClienteWSControllerRastreador();
    }

    protected function tratarSolicitacaoDeConexao($cabecalho, $socket) {
        if ($cabecalho != null && $socket != null) {
            $this->printar("Handshake Recebido:\n$cabecalho");
            preg_match('/(?<=GET \/)[^\s]*/', $cabecalho, $aux);
            $parametros = split(';', $aux[0]);
            $tipoCliente = $parametros[0];
            $id = $parametros[1];
            if ($tipoCliente != null && $id != null) {
                $cadastro = null;
                // VERIFICA SE CLIENTE JÁ ESTÁ CONECTADO
                if ($this->clienteWSController->buscarCliente($tipoCliente, $id) == null) {
                    switch ($tipoCliente) {
                        case TipoCliente::USUARIO:
                            $cadastro = ServiceLocator::getUsuarioService()->readById($id);
                            break;
                        case TipoCliente::RASTREADOR:
                            $cadastro = ServiceLocator::getRastreadorService()->readById($id);
                            break;
                    }
                    if ($cadastro != null) {
                        $this->executarHandshaking($cabecalho, $socket);
                        $clienteWS = new ClienteWSRastreador();
                        $clienteWS->setTipoCliente($tipoCliente);
                        $clienteWS->setId($id);
                        $clienteWS->setSocket($socket);
                        $clienteWS->setCadastro($cadastro);
                        $this->clienteWSController->adicionarCliente($clienteWS);
                        $this->printar("Cliente conectado (Filha).\n");
                    } else {
                        $this->printar("Usuario/Rastreador nao existe na base de dados.\n");
                    }
                } else {
                    $this->printar("Cliente ja esta conectado.\n");
                }
            } else {
                $this->printar("Parametros invalidos.\n");
            }
        }
    }

    protected function tratarMensagemRecebida($buffer, $socket) {
        if ($buffer != null && $socket != null) {
            $mensagem = $this->desmascarar($buffer);
            $this->printar("Mensagem Recebida:\n$mensagem\n");
            $valores = split(";", $mensagem);
            if (count($valores) > 0) {
                $clienteWS = $this->clienteWSController->buscarClientePorSocket($socket);
                $tipoComunicacao = $valores[0];
                switch ($tipoComunicacao) {
                    case TipoComunicacao::CONFIG_INICIAL_RASTREADOR:
                        $this->tratarRecebimentoConfigInicialRastreador($valores, $clienteWS);
                        break;
                    case TipoComunicacao::CONFIG_RASTREADOR:
                        $this->tratarRecebimentoConfigRastreador($valores, $clienteWS);
                        break;
                    case TipoComunicacao::COORDENADA:
                        $this->tratarRecebimentoCoordenada($valores, $clienteWS);
                        break;
                    case TipoComunicacao::TESTE:
                        $this->tratarRecebimentoTeste($valores, $clienteWS);
                        break;
                }
            }
        }
    }

    private function tratarRecebimentoConfigInicialRastreador($valores, $clienteWS) {
        $this->printar("CONFIGURACAO INICIAL RASTREADOR\n");

        if ($clienteWS != null) {
            switch ($clienteWS->getTipoCliente()) {
                case TipoCliente::USUARIO:
                    $serialRastreador = $valores[1];
                    $tokenRastreador = $valores[2];
                    $criteria = array();
                    $criteria[RastreadorCriteria::SERIAL_EQ] = $serialRastreador;
                    $criteria[RastreadorCriteria::TOKEN_EQ] = $tokenRastreador;
                    $rastreador = ServiceLocator::getRastreadorService()->readByCriteria($criteria)[0];
                    if ($rastreador != null) {
                        $clienteWSRas = $this->clienteWSController->buscarCliente(TipoCliente::RASTREADOR, $rastreador->getId());
                        if ($clienteWSRas != null) {
                            // VERIFICA SE USUÁRIO JÁ ESTÁ VINCULADO AO RASTREADOR
                            if ($this->verificarAcessoDeUsuarioAoRastreador($clienteWS->getCadastro(), $clienteWSRas->getCadastro()) != true) {
                                // ENVIA MENSAGEM DE CONFIGURAÇÃO INICIAL PARA RASTREADOR
                                $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' .
                                        $rastreador->getId() . ';' . $rastreador->getSerial() . ';' .
                                        $rastreador->getToken();
                                $this->enviarMensagemSocket($clienteWSRas->getSocket(), $mensagem);
                                $clienteWS->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_INICIAL_RASTREADOR);
                                $clienteWS->setSocketPar($clienteWSRas->getSocket());
                                $clienteWSRas->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_INICIAL_RASTREADOR);
                                $clienteWSRas->setSocketPar($clienteWS->getSocket());
                            } else {
                                // RETORNA MENSAGEM DE SUCESSO PARA USUÁRIO (RASTREAOR JÁ VINCULADO A USUÁRIO)
                                $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' . 2;
                                $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                            }
                        } else {
                            // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (RASTREADOR DESCONECTADO)
                            $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' . 11;
                            $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                        }
                    } else {
                        // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (SERIAL E/OU TOKEN NÃO EXISTE)
                        $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' . 10;
                        $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                    }
                    break;
                case TipoCliente::RASTREADOR:
                    $resultado = $valores[1];
                    if ($clienteWS->getTipoComunicacaoAtual() == TipoComunicacao::CONFIG_INICIAL_RASTREADOR &&
                            $clienteWS->getSocketPar() != null) {
                        $clienteWSUsu = $this->clienteWSController->buscarClientePorSocket($clienteWS->getSocketPar());
                        if ($clienteWSUsu != null) {
                            if ($resultado == 1) {
                                // TENTA VINCULAR USUÁRIO A RASTREADOR
                                $usuarioRastreador = new UsuarioRastreador();
                                $usuarioRastreador->setUsuario($clienteWSUsu->getCadastro());
                                $usuarioRastreador->setRastreador($clienteWS->getCadastro());
                                if (ServiceLocator::getUsuarioRastreadorService()->create($usuarioRastreador) == true) {
                                    $clienteWS->atualizarCadastro();
                                    $clienteWSUsu->atualizarCadastro();
                                    // RETORNA MENSAGEM DE SUCESSO PARA USUÁRIO
                                    $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' . 1;
                                    $this->enviarMensagemSocket($clienteWSUsu->getSocket(), $mensagem);
                                } else {
                                    // RETORNA MENSAGEM DE ERRO PARA USUÁRIO ()
                                    $mensagem = TipoComunicacao::CONFIG_INICIAL_RASTREADOR . ';' . 12;
                                    $this->enviarMensagemSocket($clienteWSUsu->getSocket(), $mensagem);
                                }
                            }
                            $clienteWSUsu->setTipoComunicacaoAtual(null);
                            $clienteWSUsu->setSocketPar(null);
                        }
                    }
                    $clienteWS->setTipoComunicacaoAtual(null);
                    $clienteWS->setSocketPar(null);
                    break;
            }
        }
    }

    private function tratarRecebimentoConfigRastreador($valores, $clienteWS) {
        $this->printar("CONFIGURACAO RASTREADOR\n");

        if ($clienteWS != null) {
            switch ($clienteWS->getTipoCliente()) {
                case TipoCliente::USUARIO:
                    $idRastreador = $valores[1];
                    $rastreador = ServiceLocator::getRastreadorService()->readById($idRastreador);
                    if ($rastreador != null) {
                        $clienteWSRas = $this->clienteWSController->buscarCliente(TipoCliente::RASTREADOR, $idRastreador);
                        if ($clienteWSRas != null) {
                            $estadoRastreador = $valores[2];
                            $frequenciaEnvio = $valores[3];
                            if ($estadoRastreador != null && $frequenciaEnvio != null) {
                                if ($this->verificarAcessoDeUsuarioAoRastreador($clienteWS->getCadastro(), $clienteWSRas->getCadastro()) == true) {
                                    // ENVIA MENSAGEM DE CONFIGURAÇÃO PARA RASTREADOR
                                    $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' .
                                            $estadoRastreador . ';' . $frequenciaEnvio;
                                    $this->enviarMensagemSocket($clienteWSRas->getSocket(), $mensagem);
                                    $clienteWS->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_RASTREADOR);
                                    $clienteWS->setSocketPar($clienteWSRas->getSocket());
                                    $clienteWSRas->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_RASTREADOR);
                                    $clienteWSRas->setSocketPar($clienteWS->getSocket());
                                } else {
                                    // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (SEM PERMISSÃO)
                                    $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' . 13;
                                    $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                                }
                            } else {
                                // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (PARÂMETROS INVÁLIDOS)
                                $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' . 12;
                                $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                            }
                        } else {
                            // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (RASTREADOR DESCONECTADO)
                            $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' . 11;
                            $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                        }
                    } else {
                        // RETORNA MENSAGEM DE ERRO PARA USUÁRIO (ID INVÁLIDO)
                        $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' . 10;
                        $this->enviarMensagemSocket($clienteWS->getSocket(), $mensagem);
                    }
                    break;
                case TipoCliente::RASTREADOR:
                    $resultado = $valores[1];
                    if ($clienteWS->getTipoComunicacaoAtual() == TipoComunicacao::CONFIG_RASTREADOR &&
                            $clienteWS->getSocketPar() != null) {
                        $clienteWSUsu = $this->clienteWSController->buscarClientePorSocket($clienteWS->getSocketPar());
                        if ($clienteWSUsu != null) {
                            if ($resultado == 1) {
                                // RETORNA MENSAGEM DE SUCESSO PARA USUÁRIO
                                $mensagem = TipoComunicacao::CONFIG_RASTREADOR . ';' . 1;
                                $this->enviarMensagemSocket($clienteWSUsu->getSocket(), $mensagem);
                            }
                            $clienteWSUsu->setTipoComunicacaoAtual(null);
                            $clienteWSUsu->setSocketPar(null);
                        }
                    }
                    $clienteWS->setTipoComunicacaoAtual(null);
                    $clienteWS->setSocketPar(null);
                    break;
            }
        }
    }

    private function tratarRecebimentoCoordenada($valores, $clienteWS) {
        $this->printar("COORDENADA\n");

        if ($clienteWS != null) {
            switch ($clienteWS->getTipoCliente()) {
                case TipoCliente::USUARIO:
                    break;
                case TipoCliente::RASTREADOR:
                    $latitude = $valores[1];
                    $longitude = $valores[2];
                    $hdop = $valores[3];
                    if ($latitude != null && $longitude != null && $hdop != null) {
                        $rastreador = ServiceLocator::getRastreadorService()->readById($clienteWS->getId());
                        if ($rastreador != null) {
                            $clienteWSUsu = null;
                            $mensagem = TipoComunicacao::COORDENADA . ';' . $latitude . ';' . $longitude . ';' . $hdop;
                            foreach ($rastreador->getUsuarioArray() as $usuario) {
                                $clienteWSUsu = $this->clienteWSController->buscarCliente(TipoCliente::USUARIO, $usuario->getId());
                                if ($clienteWSUsu != null) {
                                    $this->enviarMensagemSocket($clienteWSUsu->getSocket(), $mensagem);
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    private function tratarRecebimentoTeste($valores, $clienteWS) {
        $this->printar("TESTE\n");

        $funcao = $valores[1];
        switch ($funcao) {
            case 1:
                echo "Printar Clientes\n";
                var_dump($this->clienteWSController->getClienteArray());
                break;
            case 2:
                echo "Printar ClienteWS (tipoCliente, id)\n";
                var_dump($this->clienteWSController->buscarCliente($valores[2], $valores[3]));
                break;
            case 3:
                echo "Printar ClienteWS atual\n";
                var_dump($clienteWS);
                break;
        }
    }

    private function verificarAcessoDeUsuarioAoRastreador($usuario, $rastreador) {
        if ($usuario != null && $rastreador != null) {
            foreach ($usuario->getRastreadorArray() as $valor) {
                if ($valor->__toString() == $rastreador->__toString()) {
                    return true;
                }
            }
            return false;
        }
        return null;
    }

}

$servidorWS = new ServidorWSRastreador('localhost', 2502, true);
$servidorWS->inicializar();
