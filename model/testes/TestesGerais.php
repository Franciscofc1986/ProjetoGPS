<?php

include_once realpath(__DIR__) . '/../../model/service/ServiceLocator.php';
include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/RastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/CoordenadaCriteria.php';
include_once realpath(__DIR__) . '/../../model/service/RastreadorService.php';
include_once realpath(__DIR__) . '/../../model/service/CoordenadaService.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

//$coordenadaArray = obterCoordenadasDeRastreador("PQ124");
//foreach ($coordenadaArray as $coordenada) {
//    echo "$coordenada<br>";
//}

function obterCoordenadasDeRastreador($serial) {
    $criteria = array();
    $criteria[RastreadorCriteria::SERIAL_EQ] = $serial;
    $rastreadorArray = ServiceLocator::getRastreadorService()->readByCriteria($criteria);

    $criteria = array();
    $criteria[CoordenadaCriteria::RASTREADOR_FK_EQ] = $rastreadorArray[0]->getId();
    return ServiceLocator::getCoordenadaService()->readByCriteria($criteria);
}

function inserirCoordenada() {
    $coordenada = new Coordenada();
    $coordenada->setLatitude(-27.111);
    $coordenada->setLongitude(-27.222);
    $coordenada->setDataHora(date('Y-m-d H-i-s'));
    $aux = new Rastreador();
    $aux->setId(7);
    $coordenada->setRastreador($aux);

    echo ServiceLocator::getCoordenadaService()->create($coordenada);
}
