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
            $this->printar("Handshake Recebido:\n$cabecalho", true);
            preg_match('/(?<=GET \/)[^\s]*/', $cabecalho, $aux);
            $parametros = split(';', $aux[0]);
            $tipoCliente = $parametros[0];
            $id = $parametros[1];
            if ($tipoCliente != null && $id != null) {
                $cadastro = null;
                // VERIFICA SE CLIENTE JÁ ESTÁ CONECTADO
                $clienteWS = $this->clienteWSController->buscarCliente($tipoCliente, $id);
                if ($clienteWS == null) {
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
                        $this->printar($clienteWS->getCadastro()->getNome() . " se conectou.\n", false);
                    } else {
                        $this->printar("Usuario/Rastreador nao existe na base de dados.\n", false);
                    }
                } else {
                    $this->printar($clienteWS->getCadastro()->getNome() . " ja esta conectado.\n", false);
                }
            } else {
                $this->printar("Parametros invalidos.\n", false);
            }
        }
    }

    protected function tratarRecebimentoDeMensagem($buffer, $socket) {
        if ($buffer != null && $socket != null) {
            $clienteWS = $this->clienteWSController->buscarClientePorSocket($socket);
            $mensagem = $this->desmascarar($buffer);
            $this->printar("Mensagem Recebida (" . $clienteWS->getCadastro()->getNome() . "):\n$mensagem\n", true);
            $valores = split(";", $mensagem);
            $tipoComunicacao = $valores[0];
            if ($this->validarTipoComunicacao($tipoComunicacao) === true) {
                if ($this->validarValoresRecebidos($valores, $clienteWS) === true) {
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
                } else {
                    $this->printar("Valores recebidos invalidos.\n", false);
                    $resposta = array();
                    $resposta[] = $tipoComunicacao;
                    $resposta[] = 10;
                    $this->enviarValoresParaClienteWS($resposta, $clienteWS);
                }
            } else {
                $this->printar("Tipo de comunicacao invalida.\n", false);
            }
        }
    }

    protected function tratarDesconexao($socket) {
        if ($socket != null) {
            $clienteWS = $this->clienteWSController->removerClientePorSocket($socket);
            if ($clienteWS != null) {
                $this->printar($clienteWS->getCadastro()->getNome() . " se desconectou.\n", true);
            }
        }
    }

    private function validarTipoComunicacao($tipoComunicacao) {
        $resultado = false;
        if ($tipoComunicacao == TipoComunicacao::CONFIG_INICIAL_RASTREADOR ||
                $tipoComunicacao == TipoComunicacao::CONFIG_RASTREADOR ||
                $tipoComunicacao == TipoComunicacao::COORDENADA ||
                $tipoComunicacao == TipoComunicacao::TESTE) {
            $resultado = true;
        }
        return $resultado;
    }

    private function validarValoresRecebidos($valorArray, $clienteWS) {
        $valoresValidos = false;
        if ($valorArray != null && count($valorArray) > 0 && $clienteWS != null) {
            $tipoCliente = $clienteWS->getTipoCliente();
            $tipoComunicacao = $valorArray[0];
            switch ($tipoCliente) {
                case TipoCliente::USUARIO:
                    switch ($tipoComunicacao) {
                        case TipoComunicacao::CONFIG_INICIAL_RASTREADOR:
                            $serialRastreador = $valorArray[1];
                            $tokenRastreador = $valorArray[2];
                            if ($serialRastreador != null && $serialRastreador != '' &&
                                    $tokenRastreador != null && $tokenRastreador != '') {
                                $valoresValidos = true;
                            }
                            break;
                        case TipoComunicacao::CONFIG_RASTREADOR:
                            $idRastreador = $valorArray[1];
                            $estadoRastreador = $valorArray[2];
                            $frequenciaEnvio = $valorArray[3];
                            if ($idRastreador != null && $idRastreador != '' &&
                                    $estadoRastreador != null && $estadoRastreador != '' &&
                                    $frequenciaEnvio != null && $frequenciaEnvio != '') {
                                $valoresValidos = true;
                            }
                            break;
                        case TipoComunicacao::TESTE:
                            $valoresValidos = true;
                            break;
                    }
                    break;
                case TipoCliente::RASTREADOR:
                    switch ($tipoComunicacao) {
                        case TipoComunicacao::CONFIG_INICIAL_RASTREADOR:
                            $resultado = $valorArray[1];
                            if ($resultado != null && $resultado != '') {
                                $valoresValidos = true;
                            }
                            break;
                        case TipoComunicacao::CONFIG_RASTREADOR:
                            $resultado = $valorArray[1];
                            if ($resultado != null && $resultado != '') {
                                $valoresValidos = true;
                            }
                            break;
                        case TipoComunicacao::COORDENADA:
                            $latitude = $valorArray[1];
                            $longitude = $valorArray[2];
                            $hdop = $valorArray[3];
                            if ($latitude != null && $latitude != '' &&
                                    $longitude != null && $longitude != '' &&
                                    $hdop != null && $hdop != '') {
                                $valoresValidos = true;
                            }
                            break;
                    }
                    break;
            }
        }
        return $valoresValidos;
    }

    private function tratarRecebimentoConfigInicialRastreador($valores, $clienteWS) {
        $this->printar("CONFIGURACAO INICIAL RASTREADOR\n", false);

        $clienteWSDestino = null;
        $resposta = array();
        $resposta[] = TipoComunicacao::CONFIG_INICIAL_RASTREADOR;

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
                            // MENSAGEM DE CONFIGURAÇÃO INICIAL PARA RASTREADOR                               
                            $clienteWSDestino = $clienteWSRas;
                            $resposta[] = $rastreador->getId();
                            $resposta[] = $rastreador->getSerial();
                            $resposta[] = $rastreador->getToken();

                            $clienteWS->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_INICIAL_RASTREADOR);
                            $clienteWS->setSocketPar($clienteWSRas->getSocket());
                            $clienteWSRas->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_INICIAL_RASTREADOR);
                            $clienteWSRas->setSocketPar($clienteWS->getSocket());
                        } else {
                            // MENSAGEM DE SUCESSO PARA USUÁRIO (RASTREAOR JÁ VINCULADO A USUÁRIO)
                            $clienteWSDestino = $clienteWS;
                            $resposta[] = $rastreador->getId();
                            $resposta[] = 2;
                        }
                    } else {
                        // MENSAGEM DE ERRO PARA USUÁRIO (RASTREADOR DESCONECTADO)
                        $clienteWSDestino = $clienteWS;
                        $resposta[] = $rastreador->getId();
                        $resposta[] = 21;
                    }
                } else {
                    // MENSAGEM DE ERRO PARA USUÁRIO (SERIAL E/OU TOKEN NÃO EXISTE)
                    $clienteWSDestino = $clienteWS;
                    $resposta[] = 0;
                    $resposta[] = 20;
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
                                // MENSAGEM DE SUCESSO PARA USUÁRIO (OK)
                                $clienteWSDestino = $clienteWSUsu;
                                $resposta[] = $clienteWS->getId();
                                $resposta[] = 1;
                            } else {
                                // MENSAGEM DE ERRO PARA USUÁRIO (VICULAÇÃO USUÁRIO/RASTREADOR FALHOU)
                                $clienteWSDestino = $clienteWSUsu;
                                $resposta[] = $clienteWS->getId();
                                $resposta[] = 22;
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

        if ($clienteWSDestino != null && count($resposta) > 0) {
            $this->enviarValoresParaClienteWS($resposta, $clienteWSDestino);
        }
    }

    private function tratarRecebimentoConfigRastreador($valores, $clienteWS) {
        $this->printar("CONFIGURACAO RASTREADOR\n", false);

        $clienteWSDestino = null;
        $resposta = array();
        $resposta[] = TipoComunicacao::CONFIG_RASTREADOR;

        switch ($clienteWS->getTipoCliente()) {
            case TipoCliente::USUARIO:
                $idRastreador = $valores[1];
                $rastreador = ServiceLocator::getRastreadorService()->readById($idRastreador);
                if ($rastreador != null) {
                    $clienteWSRas = $this->clienteWSController->buscarCliente(TipoCliente::RASTREADOR, $idRastreador);
                    if ($clienteWSRas != null) {
                        $estadoRastreador = $valores[2];
                        $frequenciaEnvio = $valores[3];
                        if ($this->verificarAcessoDeUsuarioAoRastreador($clienteWS->getCadastro(), $clienteWSRas->getCadastro()) == true) {
                            // MENSAGEM DE CONFIGURAÇÃO PARA RASTREADOR
                            $clienteWSDestino = $clienteWSRas;
                            $resposta[] = $estadoRastreador;
                            $resposta[] = $frequenciaEnvio;

                            $clienteWS->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_RASTREADOR);
                            $clienteWS->setSocketPar($clienteWSRas->getSocket());
                            $clienteWSRas->setTipoComunicacaoAtual(TipoComunicacao::CONFIG_RASTREADOR);
                            $clienteWSRas->setSocketPar($clienteWS->getSocket());
                        } else {
                            // MENSAGEM DE ERRO PARA USUÁRIO (SEM PERMISSÃO)
                            $clienteWSDestino = $clienteWS;
                            $resposta[] = $idRastreador;
                            $resposta[] = 22;
                        }
                    } else {
                        // MENSAGEM DE ERRO PARA USUÁRIO (RASTREADOR DESCONECTADO)
                        $clienteWSDestino = $clienteWS;
                        $resposta[] = $idRastreador;
                        $resposta[] = 21;
                    }
                } else {
                    // MENSAGEM DE ERRO PARA USUÁRIO (ID INVÁLIDO)
                    $clienteWSDestino = $clienteWS;
                    $resposta[] = $idRastreador;
                    $resposta[] = 20;
                }
                break;
            case TipoCliente::RASTREADOR:
                $resultado = $valores[1];
                if ($clienteWS->getTipoComunicacaoAtual() == TipoComunicacao::CONFIG_RASTREADOR &&
                        $clienteWS->getSocketPar() != null) {
                    $clienteWSUsu = $this->clienteWSController->buscarClientePorSocket($clienteWS->getSocketPar());
                    if ($clienteWSUsu != null) {
                        if ($resultado == 1) {
                            // MENSAGEM DE SUCESSO PARA USUÁRIO (OK)
                            $clienteWSDestino = $clienteWSUsu;
                            $resposta[] = $clienteWS->getId();
                            $resposta[] = 1;
                        }
                        $clienteWSUsu->setTipoComunicacaoAtual(null);
                        $clienteWSUsu->setSocketPar(null);
                    }
                }
                $clienteWS->setTipoComunicacaoAtual(null);
                $clienteWS->setSocketPar(null);
                break;
        }

        if ($clienteWSDestino != null && count($resposta) > 0) {
            $this->enviarValoresParaClienteWS($resposta, $clienteWSDestino);
        }
    }

    private function tratarRecebimentoCoordenada($valores, $clienteWS) {
        $this->printar("COORDENADA\n", false);

        $clienteWSDestino = null;
        $resposta = array();
        $resposta[] = TipoComunicacao::COORDENADA;

        switch ($clienteWS->getTipoCliente()) {
            case TipoCliente::RASTREADOR:
                $rastreador = ServiceLocator::getRastreadorService()->readById($clienteWS->getId());
                if ($rastreador != null) {
                    $resposta[] = $clienteWS->getId();
                    $resposta[] = $valores[1]; // latitude
                    $resposta[] = $valores[2]; // longitude
                    $resposta[] = $valores[3]; // hdop
                    foreach ($rastreador->getUsuarioArray() as $usuario) {
                        $clienteWSDestino = $this->clienteWSController->buscarCliente(TipoCliente::USUARIO, $usuario->getId());
                        if ($clienteWSDestino != null) {
                            $this->enviarValoresParaClienteWS($resposta, $clienteWSDestino);
                        }
                    }
                }
                break;
        }
    }

    private function tratarRecebimentoTeste($valores, $clienteWS) {
        $this->printar("TESTE\n", false);

        $funcao = $valores[1];
        switch ($funcao) {
            case 1:
                $this->printar("Printar Clientes\n", false);
                var_dump($this->clienteWSController->getClienteArray());
                break;
            case 2:
                $this->printar("Printar ClienteWS (tipoCliente, id)\n", false);
                var_dump($this->clienteWSController->buscarCliente($valores[2], $valores[3]));
                break;
            case 3:
                $this->printar("Printar ClienteWS atual\n", false);
                var_dump($clienteWS);
                break;
        }
    }

    private function enviarValoresParaClienteWS($valorArray, $clienteWSDestino) {
        if ($valorArray != null && count($valorArray) > 0 && $clienteWSDestino != null) {
            $mensagem = implode(";", $valorArray);
            $this->enviarMensagemSocket($clienteWSDestino->getSocket(), $mensagem);
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
