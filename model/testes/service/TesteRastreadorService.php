<?php

include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/RastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/RastreadorService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

testeReadByCriteria();

function testeCreate() {
    $entity = new Rastreador();
    $entity->setSerial("PQ262");
    $entity->setNome("Rastreador teste");
    $entity->setDataHora(date('Y-m-d H-i-s'));

    $coordenada = new Coordenada();
    $coordenada->setId(12);
    $entity->setUltimaCoordenada($coordenada);

    echo "Resultado: " . ServiceLocator::getRastreadorService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getRastreadorService()->delete(2);
}

function testeUpdate() {
    $entity = new Rastreador();
    $entity->setId(14);
    $entity->setSerial("PQ400");
    $entity->setNome("Rastreador teste");
    $entity->setDataHora(date('Y-m-d H-i-s'));
    $coordenada = new Coordenada();
    $coordenada->setId(7);
    $entity->setUltimaCoordenada($coordenada);

    echo "Resultado: " . ServiceLocator::getRastreadorService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getRastreadorService()->readById(10);
}

function testeReadByCriteria() {
    $criteria = array();
//    $criteria[RastreadorCriteria::DATA_HORA_LK] = date('Y-m-d');
    $entityArray = ServiceLocator::getRastreadorService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
