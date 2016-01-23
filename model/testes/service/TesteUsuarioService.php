<?php

include_once realpath(__DIR__) . '/../../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/UsuarioCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/UsuarioService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

function testeCreate() {
    $entity = new Usuario();
    $entity->setLogin("UsuarioTeste");
    $entity->setSenha("senhaTeste");
    $entity->setNome("Usuario Teste");
    echo "Resultado: " . ServiceLocator::getUsuarioService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getUsuarioService()->delete(21);
}

function testeUpdate() {
    $entity = new Usuario();
    $entity->setId(6);
    $entity->setLogin("UsuarioTeste");
    $entity->setSenha("senhaTeste");
    $entity->setNome("Usuario Teste");
    echo "Resultado: " . ServiceLocator::getUsuarioService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getUsuarioService()->readById(7);
}

function testeReadByCriteria() {
    $criteria = array();
    $criteria[UsuarioCriteria::NOME_LK] = "B";
    $entityArray = ServiceLocator::getUsuarioService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
