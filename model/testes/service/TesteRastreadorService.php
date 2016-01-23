<?php

include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/RastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/RastreadorService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

function testeCreate() {
    $entity = new Rastreador();
    $entity->setSerial("PQ482");
    $entity->setNome("Rastreador teste");
    $entity->setPublico(true);

    echo "Resultado: " . ServiceLocator::getRastreadorService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getRastreadorService()->delete(5);
}

function testeUpdate() {
    $entity = new Rastreador();
    $entity->setId(5);
    $entity->setSerial("PQ406");
    $entity->setNome("Rastreador teste");
    $entity->setPublico(true);

    echo "Resultado: " . ServiceLocator::getRastreadorService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getRastreadorService()->readById(2);
}

function testeReadByCriteria() {
    $criteria = array();
    $criteria[RastreadorCriteria::PUBLICO_EQ] = true;
    $entityArray = ServiceLocator::getRastreadorService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
