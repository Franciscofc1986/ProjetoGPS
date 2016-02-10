<?php

include_once realpath(__DIR__) . '/../model/service/ServiceLocator.php';
include_once realpath(__DIR__) . '/../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../model/entity/Rastreador.php';
include_once realpath(__DIR__) . './ClienteWS.php';
include_once realpath(__DIR__) . './ClienteWSController.php';
include_once realpath(__DIR__) . './TipoCliente.php';
include_once realpath(__DIR__) . './TipoComunicacao.php';
include_once realpath(__DIR__) . './Servicos.php';

class ServidorWS {

    protected $ip;
    protected $porta;
    protected $socketPrincipal;
    protected $clienteWSController;

    public function __construct($ip = 'localhost', $porta = 5432) {
        $this->ip = $ip;
        $this->porta = $porta;
        $this->clienteWSController = new ClienteWSController();
    }

    public function inicializar() {
        // TCP/IP stream
        $this->socketPrincipal = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // Porta reusável
        socket_set_option($this->socketPrincipal, SOL_SOCKET, SO_REUSEADDR, 1);
        // Vincula socket com ip/porta
        // Obs: o ip 0.0.0.0 neste caso indica todos os ip's locais (ex: 127.0.0.1, 192.168.254.100, etc)
        socket_bind($this->socketPrincipal, "0.0.0.0", $this->porta);
        // Inicia "escuta" por conexões
        socket_listen($this->socketPrincipal);

        while (true) {
            $socketsRead = $this->getSocketsRead();
            $socketsWrite = $this->getSocketsWrite();
            $socketsExcept = $this->getSocketsExcept();
            socket_select($socketsRead, $socketsWrite, $socketsExcept, 0, 10);
            $this->onSocketsAlterados($socketsRead, $socketsWrite, $socketsExcept);
        }

        socket_close($this->socketPrincipal);
    }

    protected function getSocketsRead() {
        $socketsRead = $this->clienteWSController->getSockets();
        $socketsRead[] = $this->socketPrincipal;
        return $socketsRead;
    }

    protected function getSocketsWrite() {
        return null;
    }

    protected function getSocketsExcept() {
        return null;
    }

    protected function onSocketsAlterados($socketsRead, $socketsWrite, $socketsExcept) {

        if (count($socketsRead) > 0) {
            // Caso socket principal foi alterado (Receptor de conexões)
            if (in_array($this->socketPrincipal, $socketsRead)) {
                $novoSocket = socket_accept($this->socketPrincipal);
                $this->tratarSolicitacaoDeConexao($novoSocket);
                // Retira socket ouvinte de lista de socketsAlterados
                $chave = array_search($this->socketPrincipal, $socketsRead);
                unset($socketsRead[$chave]);
            }

            foreach ($socketsRead as $socketRead) {
                // Verifica mensagens recebidas
                while (socket_recv($socketRead, $buffer, 1024, 0) >= 1) {
                    $this->tratarMensagemRecebida($buffer, $socketRead);
                    break 2;
                }

                // Verifica se cliente continua conectado
                $buffer = @socket_read($socketRead, 1024, PHP_NORMAL_READ);
                if ($buffer === false) {
                    $this->clienteWSController->removerClientePorSocket($socketRead);
                }
            }
        }
    }

    protected function tratarSolicitacaoDeConexao($socket) {
        if ($socket != null) {
            $cabecalho = socket_read($socket, 1024);
            if ($cabecalho != null) {
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
                            $clienteWS = new ClienteWS();
                            $clienteWS->setTipoCliente($tipoCliente);
                            $clienteWS->setId($id);
                            $clienteWS->setSocket($socket);
                            $clienteWS->setCadastro($cadastro);
                            $this->clienteWSController->adicionarCliente($clienteWS);
                        }
                    }
                }
            }
        }
    }

////////////////////////////////////////////////////////////////////////////////
// PROTOCOLOS DE COMUNICAÇÃO
////////////////////////////////////////////////////////////////////////////////
    protected function tratarMensagemRecebida($buffer, $socket) {
        $texto = $this->desmascarar($buffer);
        $valores = split(";", $texto);
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

    private function tratarRecebimentoConfigInicialRastreador($valores, $clienteWS) {
        echo "CONFIGURACAO INICIAL RASTREADOR\n";

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
        echo "CONFIGURACAO RASTREADOR\n";

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
        echo "COORDENADA\n";

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
        echo "TESTE\n";

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

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

    protected function enviarMensagemSockets($clienteArray, $mensagem) {
        foreach ($clienteArray as $cliente) {
            $this->enviarMensagemSocket($cliente, $mensagem);
        }
        return true;
    }

    protected function enviarMensagemSocket($cliente, $mensagem) {
        $msgMascarada = $this->mascarar($mensagem);
        return @socket_write($cliente, $msgMascarada, strlen($msgMascarada));
    }

    protected function mascarar($mensagem) {
        $byte0 = 0x81;
        $tamanho = strlen($mensagem);
        if ($tamanho <= 125)
            $cabecalho = pack('CC', $byte0, $tamanho);
        elseif ($tamanho > 125 && $tamanho < 65536)
            $cabecalho = pack('CCn', $byte0, 126, $tamanho);
        elseif ($tamanho >= 65536)
            $cabecalho = pack('CCNN', $byte0, 127, $tamanho);
        return $cabecalho . $mensagem;
    }

    protected function desmascarar($mensagem) {
        $tamanho = $mensagem[1];
        if ($tamanho == 126) {
            $mascaras = substr($mensagem, 4, 4);
            $dados = substr($mensagem, 8);
        } elseif ($tamanho == 127) {
            $mascaras = substr($mensagem, 10, 4);
            $dados = substr($mensagem, 14);
        } else {
            $mascaras = substr($mensagem, 2, 4);
            $dados = substr($mensagem, 6);
        }
        $mensagem = "";
        for ($i = 0; $i < strlen($dados); ++$i) {
            $mensagem .= $dados[$i] ^ $mascaras[$i % 4];
        }
        return $mensagem;
    }

    protected function executarHandshaking($cabecalhoRecebido, $novoSocket) {
        $cabecalhos = array();
        $linhas = preg_split('/\r\n/', $cabecalhoRecebido);
        foreach ($linhas as $linha) {
            $linha = rtrim($linha);
            if (preg_match('/\A(\S+): (.*)\z/', $linha, $matches)) {
                $cabecalhos[$matches[1]] = $matches[2];
            }
        }
        $secKey = $cabecalhos['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
                "Upgrade: websocket\r\n" .
                "Connection: Upgrade\r\n" .
                "WebSocket-Origin: $this->ip\r\n" .
                "WebSocket-Location: ws://$this->ip:$this->porta\r\n" .
                "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        socket_write($novoSocket, $upgrade, strlen($upgrade));
    }

}

$servidorWS = new ServidorWS('localhost', 2502);
$servidorWS->inicializar();
