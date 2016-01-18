<?php

inicializar();

function inicializar() {
    $ip = 'localhost';
    $porta = '2502';
    // TCP/IP stream
    $socketPrincipal = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    // Porta reusável
    socket_set_option($socketPrincipal, SOL_SOCKET, SO_REUSEADDR, 1);
    // Vincula socket com ip/porta
    // Obs: o ip 0.0.0.0 neste caso india todos os ip's locais (ex: 127.0.0.1, 192.168.254.100, etc)
    socket_bind($socketPrincipal, "0.0.0.0", $porta);
    // Inicia "escuta" por conexões
    socket_listen($socketPrincipal);

    $clientes = array($socketPrincipal);

    while (true) {
        $socketsRead = $clientes;
        $socketsWrite = null;
        $socketsExcept = null;
        // Retorna sockets alterados
        socket_select($socketsRead, $socketsWrite, $socketsExcept, 0, 10);

        // Caso socket "ouvinte" foi alterado
        if (in_array($socketPrincipal, $socketsRead)) {
            // Verifica solicitação de conexão
            $novoSocket = socket_accept($socketPrincipal);
            $clientes[] = $novoSocket;
            $cabecalho = socket_read($novoSocket, 1024);
            executarHandshaking($cabecalho, $novoSocket, $ip, $porta);

            // Retira socket ouvinte de lista de socketsAlterados
            $chave = array_search($socketPrincipal, $socketsRead);
            unset($socketsRead[$chave]);
        }

        foreach ($socketsRead as $socketRead) {
            // Verifica dados recebidos
            while (socket_recv($socketRead, $buffer, 1024, 0) >= 1) {
                $texto = desmascarar($buffer);
                if (isJson($texto)) {
                    $textoJson = json_decode($texto);
                    $id = $textoJson->id;
                    $lat = $textoJson->lat;
                    $lon = $textoJson->lon;
                    enviarMensagem($clientes, "$id;$lat;$lon");
                } else {
                    enviarMensagem($clientes, $texto);
                }
                break 2;
            }

            // Verifica se cliente continua conectado
            $buffer = @socket_read($socketRead, 1024, PHP_NORMAL_READ);
            if ($buffer === false) {
                // Remove cliente da lista de clientes
                $chave = array_search($socketRead, $clientes);
                unset($clientes[$chave]);
            }
        }
    }
    socket_close($socketPrincipal);
}

function isJson($string) {
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

function strToHex($string) {
    $hex = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0' . $hexCode, -2) . ' ';
    }
    return $hex;
}

function enviarMensagem($clientes, $mensagem) {
    $msgCriptografada = mascarar($mensagem);
    foreach ($clientes as $cliente) {
        @socket_write($cliente, $msgCriptografada, strlen($msgCriptografada));
    }
    return true;
}

function mascarar($mensagem) {
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

function desmascarar($mensagem) {
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

function executarHandshaking($cabecalhoRecebido, $novoSocket, $ip, $porta) {
    $cabecalhos = array();
    $linhas = preg_split("/\r\n/", $cabecalhoRecebido);
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
            "WebSocket-Origin: $ip\r\n" .
            "WebSocket-Location: ws://$ip:$porta\r\n" .
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
    socket_write($novoSocket, $upgrade, strlen($upgrade));
}
