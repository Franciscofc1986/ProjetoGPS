<?php

include_once realpath(__DIR__) . '/../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../servidorWS/ClienteWSRastreador.php';
include_once realpath(__DIR__) . '/../servidorWS/TipoCliente.php';

function compararCoordenadas() {
    $coordenada = new Coordenada();
    $coordenada->setId(36);
    $coordenada->setLatitude(25.6215);
    $coordenada->setLongitude(-23.6514);
    $coordenada->setHdop(1.0256);

    $rastreador = new Rastreador();
    $rastreador->setId(1);
    $coordenada->setRastreador($rastreador);

    $coordenada2 = new Coordenada();
    $coordenada2->setId(36);
    $coordenada2->setLatitude(25.6215);
    $coordenada2->setLongitude(-23.6514);
    $coordenada2->setHdop(1.0256);

    $rastreador2 = new Rastreador();
    $rastreador2->setId(2);
    $coordenada2->setRastreador($rastreador2);

    var_dump($coordenada->comparar($coordenada2));
    echo "<br><br>";
    var_dump($coordenada == $coordenada2);
}

function compararUsuarioRastreador() {
    $usuRas = new UsuarioRastreador();
    $usuRas->setId(14);

    $usu = new Usuario();
    $usu->setId(2);
    $usu->setLogin("Creuza");
    $usu->setNome("Creuza");
    $usu->setSenha("123123");
    $usuRas->setUsuario($usu);

    $ras = new Rastreador();
    $ras->setId(5);
    $ras->setNome("Rastreador 2");
    $ras->setPublico(false);
    $ras->setSerial("PQ123");
    $ras->setToken("TK148");
    $usuRas->setRastreador($ras);

    $usuRas2 = new UsuarioRastreador();
    $usuRas2->setId(14);

    $usu2 = new Usuario();
    $usu2->setId(2);
    $usu2->setLogin("Creuza");
    $usu2->setNome("Creuza");
    $usu2->setSenha("123123");
    $usuRas2->setUsuario($usu2);

    $ras2 = new Rastreador();
    $ras2->setId(5);
    $ras2->setNome("Rastreador 2");
    $ras2->setPublico(false);
    $ras2->setSerial("PQ123");
    $ras2->setToken("TK148");
    $usuRas2->setRastreador($ras2);

    var_dump($usuRas->comparar($usuRas2));
    echo "<br><br>";
    var_dump($usuRas == $usuRas2);
}

compararClienteWS();

function compararClienteWS() {
    $cliente = new ClienteWSRastreador();
    $cliente->setId(10);
    $cliente->setTipoCliente(TipoCliente::USUARIO);

    $usu = new Usuario();
    $usu->setId(2);
    $usu->setLogin("Creuza");
    $usu->setNome("Creuza");
    $usu->setSenha("123123");
    $cliente->setCadastro($usu);


    $cliente2 = new ClienteWSRastreador();
    $cliente2->setId(10);
    $cliente2->setTipoCliente(TipoCliente::USUARIO);

    $usu2 = new Usuario();
    $usu2->setId(2);
    $usu2->setLogin("Creuza");
    $usu2->setNome("Creuza");
    $usu2->setSenha("123123");
    $cliente2->setCadastro($usu2);

    var_dump($cliente->comparar($cliente2));
    echo "<br><br>";
    var_dump($cliente == $cliente2);
}
