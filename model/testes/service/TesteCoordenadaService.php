<?php

include_once realpath(__DIR__) . '/../../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/CoordenadaCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/CoordenadaService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

testeReadByCriteria();

function testeCreate() {
    $entity = new Coordenada();
    $entity->setLatitude(-12);
    $entity->setLongitude(-12);
    $entity->setDataHora(date('Y-m-d H-i-s'));

    $rastreador = new Rastreador();
    $rastreador->setId(3);
    $entity->setRastreador($rastreador);

    echo "Resultado: " . ServiceLocator::getCoordenadaService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getCoordenadaService()->delete(2);
}

function testeUpdate() {
    $entity = new Coordenada();
    $entity->setId(15);
    $entity->setLatitude(25);
    $entity->setLongitude(25);
    $entity->setDataHora(date('Y-m-d H-i-s'));

    $rastreador = new Rastreador();
    $rastreador->setId(3);
    $entity->setRastreador($rastreador);

    echo "Resultado: " . ServiceLocator::getCoordenadaService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getCoordenadaService()->readById(7);
}

function testeReadByCriteria() {
    $criteria = array();
    $criteria[CoordenadaCriteria::RASTREADOR_FK_EQ] = 3;
    $entityArray = ServiceLocator::getCoordenadaService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
