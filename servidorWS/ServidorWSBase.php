<?php

include_once realpath(__DIR__) . './ClienteWSBase.php';
include_once realpath(__DIR__) . './ClienteWSControllerBase.php';

class ServidorWSBase {

    protected $ip;
    protected $porta;
    protected $socketPrincipal;
    protected $clienteWSController;
    protected $printarEventos;

    public function __construct($ip = 'localhost', $porta = 5432, $printarEventos = false) {
        $this->ip = $ip;
        $this->porta = $porta;
        $this->clienteWSController = new ClienteWSControllerBase();
        $this->printarEventos = $printarEventos;
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

        // Caso socket principal foi alterado (Receptor de conexões)
        if (in_array($this->socketPrincipal, $socketsRead)) {
            $socket = socket_accept($this->socketPrincipal);
            if ($socket != null) {
                $cabecalho = socket_read($socket, 1024);
                $this->tratarSolicitacaoDeConexao($cabecalho, $socket);
            }
            // Retira socket ouvinte de lista de socketsAlterados
            $chave = array_search($this->socketPrincipal, $socketsRead);
            unset($socketsRead[$chave]);
        }

        foreach ($socketsRead as $socketRead) {
            // Verifica mensagens recebidas
            while (socket_recv($socketRead, $buffer, 1024, 0) >= 1) {
                $this->tratarRecebimentoDeMensagem($buffer, $socketRead);
                break 2;
            }

            // Verifica se cliente continua conectado
            $buffer = @socket_read($socketRead, 1024, PHP_NORMAL_READ);
            if ($buffer === false) {
                $this->tratarDesconexao($socketRead);
            }
        }
    }

    protected function tratarSolicitacaoDeConexao($cabecalho, $socket) {
        if ($cabecalho != null && $socket != null) {
            $this->printar("Handshake Recebido:\n$cabecalho", true, true);
            $this->executarHandshaking($cabecalho, $socket);
            $clienteWS = new ClienteWSBase();
            $clienteWS->setSocket($socket);
            $this->clienteWSController->adicionarCliente($clienteWS);
            $this->printar("Cliente conectado.\n", false, false);
        }
    }

    protected function tratarRecebimentoDeMensagem($buffer, $socket) {
        if ($buffer != null && $socket != null) {
            $mensagem = $this->desmascarar($buffer);
            $this->printar("Mensagem Recebida:\n$mensagem\n", true, true);
        }
    }

    protected function tratarDesconexao($socket) {
        if ($socket != null) {
            $clienteWS = $this->clienteWSController->removerClientePorSocket($socket);
            if ($clienteWS != null) {
                $this->printar("Cliente desconectado.\n", true, true);
            }
        }
    }

    protected function enviarMensagemSocketArray($socketArray, $mensagem) {
        foreach ($socketArray as $cliente) {
            $this->enviarMensagemSocket($cliente, $mensagem);
        }
        return true;
    }

    protected function enviarMensagemSocket($socket, $mensagem) {
        $msgMascarada = $this->mascarar($mensagem);
        $msgEnviada = @socket_write($socket, $msgMascarada, strlen($msgMascarada));
        if ($msgEnviada !== false) {
            $this->printar("Mensagem Enviada:\n$mensagem\n", true, true);
        }
        return $msgEnviada;
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
        $this->printar("Handshake Enviado:\n$upgrade", false, false);
    }

    protected function printar($mensagem, $inserirBarra = true, $inserirHora = true) {
        if ($this->printarEventos) {
            if ($inserirBarra === true) {
                echo "--------------------------------------------------------------\n";
            }
            if ($inserirHora === true) {
                echo "(" . date('Y/m/d H:i:s') . ")\n";
            }
            echo $mensagem;
        }
    }

}
