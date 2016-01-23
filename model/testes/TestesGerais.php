<?php

include_once realpath(__DIR__) . '/../../model/service/ServiceLocator.php';
include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/RastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/CoordenadaCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioRastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/service/RastreadorService.php';
include_once realpath(__DIR__) . '/../../model/service/CoordenadaService.php';
include_once realpath(__DIR__) . '/../../model/service/UsuarioService.php';
include_once realpath(__DIR__) . '/../../model/service/UsuarioRastreadorService.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

function obterCoordenadasDeRastreador($serial) {
    $criteria = array();
    $criteria[RastreadorCriteria::ID_EQ] = 4;
    $rastreadorArray = ServiceLocator::getRastreadorService()->readByCriteria($criteria);

    $criteria = array();
    $criteria[CoordenadaCriteria::RASTREADOR_FK_EQ] = $rastreadorArray[0]->getId();
    return ServiceLocator::getCoordenadaService()->readByCriteria($criteria);
}

function inserirCoordenada() {
    for ($i = 0; $i < 50000; $i++) {
        $coordenada = new Coordenada();
        $coordenada->setLatitude(rand(-20.00001, -25.99999));
        $coordenada->setLongitude(rand(-20.00001, -25.99999));
        $coordenada->setDataHora(date('Y-m-d H-i-s'));
        $coordenada->setHdop(rand(0.00001, 2.99999));
        $aux = new Rastreador();
        $aux->setId(rand(1, 10));
        $coordenada->setRastreador($aux);
        echo ServiceLocator::getCoordenadaService()->create($coordenada) . "/$coordenada, ";
    }
}

function inserirArrayCoordenada() {
    $inicio = microtime(true);

    $coordenadaArray = array();
    for ($i = 0; $i < 50000; $i++) {
        $coordenada = new Coordenada();
        $coordenada->setLatitude(rand(-20.00001, -25.99999));
        $coordenada->setLongitude(rand(-20.00001, -25.99999));
        $coordenada->setDataHora(date('Y-m-d H-i-s'));
        $coordenada->setHdop(rand(0.00001, 2.99999));
        $aux = new Rastreador();
        $aux->setId(rand(1, 10));
        $coordenada->setRastreador($aux);
        $coordenadaArray[] = $coordenada;
    }
    echo ServiceLocator::getCoordenadaService()->create($coordenadaArray);

    $total = microtime(true) - $inicio;
    echo "<br>Tempo de execução: $total";
}

function inserirUsuario() {
    for ($i = 0; $i < 50000; $i++) {
        $usuario = new Usuario();
        $usuario->setLogin(rand());
        $usuario->setSenha(sha1(rand()));
        echo ServiceLocator::getUsuarioService()->create($usuario) . "/" . $usuario->getId() . ", ";
    }
    echo "<br> I = $i";
}

function inserirRastreador() {
    for ($i = 0; $i < 50000; $i++) {
        $ratreador = new Rastreador();
        $ratreador->setSerial(rand());
        $ratreador->setNome(rand());
        $ratreador->setPublico(false);
        echo ServiceLocator::getRastreadorService()->create($ratreador) . "/" . $ratreador->getId() . ", ";
    }
    echo "<br> I = $i";
}

function vincularUsuarioRastreador() {
    $usuario = ServiceLocator::getUsuarioService()->readById(9);
    $rastreador = ServiceLocator::getRastreadorService()->readById(1);
    if ($usuario != null && $rastreador != null) {
        $usuarioRastreador = new UsuarioRastreador();
        $usuarioRastreador->setUsuario($usuario);
        $usuarioRastreador->setRastreador($rastreador);
        echo ServiceLocator::getUsuarioRastreadorService()->create($usuarioRastreador);
    }
}

function desvincularUsuarioRastreador() {
    $usuario = ServiceLocator::getUsuarioService()->readById(3);
    if ($usuario != null) {
        foreach ($usuario->getRastreadorArray() as $rastreador) {
            $criteria = array();
            $criteria[UsuarioRastreadorCriteria::USUARIO_FK_EQ] = $usuario->getId();
            $criteria[UsuarioRastreadorCriteria::RASTREADOR_FK_EQ] = $rastreador->getId();
            $usuarioRastreadorArray = ServiceLocator::getUsuarioRastreadorService()->readByCriteria($criteria);
            foreach ($usuarioRastreadorArray as $usuarioRastreador) {
                echo ServiceLocator::getUsuarioRastreadorService()->delete($usuarioRastreador->getId());
            }
        }
    }
}
