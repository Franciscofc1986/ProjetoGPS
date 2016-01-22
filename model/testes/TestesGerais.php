<?php

include_once realpath(__DIR__) . '/../../model/service/ServiceLocator.php';
include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/RastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/CoordenadaCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioCriteria.php';
include_once realpath(__DIR__) . '/../../model/service/RastreadorService.php';
include_once realpath(__DIR__) . '/../../model/service/CoordenadaService.php';
include_once realpath(__DIR__) . '/../../model/service/UsuarioService.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

//$coordenadaArray = obterCoordenadasDeRastreador("PQ129");
//foreach ($coordenadaArray as $coordenada) {
//    echo "$coordenada<br>";
//}

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
        $coordenada->setLatitude(rand(-21000, -23000));
        $coordenada->setLongitude(rand(-21000, -23000));
        $coordenada->setDataHora(date('Y-m-d H-i-s'));
        $aux = new Rastreador();
        $aux->setId(rand(1, 10));
        $coordenada->setRastreador($aux);
        echo ServiceLocator::getCoordenadaService()->create($coordenada) . "/" . $coordenada->getId() . ", ";
    }
    echo "<br> I = $i";
}

function inserirUsuario() {
    for ($i = 0; $i < 50000; $i++) {
        $usuario = new Usuario();
        $usuario->setLogin(rand());
        $usuario->setSenha(rand());
        echo ServiceLocator::getUsuarioService()->create($usuario) . "/" . $usuario->getId() . ", ";
    }
    echo "<br> I = $i";
}

function inserirRastreador() {
    for ($i = 0; $i < 50000; $i++) {
        $ratreador = new Rastreador();
        $ratreador->setSerial(rand());
        $ratreador->setDataHora(date('Y-m-d H-i-s'));
        echo ServiceLocator::getRastreadorService()->create($ratreador) . "/" . $ratreador->getId() . ", ";
    }
    echo "<br> I = $i";
}
