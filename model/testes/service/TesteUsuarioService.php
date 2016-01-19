<?php

include_once realpath(__DIR__) . '/../../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/UsuarioCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/UsuarioService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

testeReadByCriteria();

function testeCreate() {
    $entity = new Usuario();
    $entity->setLogin("UsuarioTeste");
    $entity->setSenha("senhaTeste");
    $entity->setNome("Usuario Teste");
    $entity->setDataHora(date('Y-m-d H-i-s'));
    echo "Resultado: " . ServiceLocator::getUsuarioService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getUsuarioService()->delete(2);
}

function testeUpdate() {
    $entity = new Usuario();
    $entity->setId(3);
    $entity->setLogin("UsuarioTeste");
    $entity->setSenha("senhaTeste");
    $entity->setNome("Usuario Teste");
    $entity->setDataHora(date('Y-m-d H-i-s'));
    echo "Resultado: " . ServiceLocator::getUsuarioService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getUsuarioService()->readById(7);
}

function testeReadByCriteria() {
    $criteria = array();
    $criteria[UsuarioCriteria::NOME_LK] = "B";
    $criteria[UsuarioCriteria::SENHA_EQ] = "1111";
    $entityArray = ServiceLocator::getUsuarioService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
