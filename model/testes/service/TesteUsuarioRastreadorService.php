<?php

include_once realpath(__DIR__) . '/../../../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/UsuarioRastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../../model/service/UsuarioRastreadorService.php';
include_once realpath(__DIR__) . '/../../../model/service/ServiceLocator.php';

testeReadByCriteria();

function testeCreate() {
    $entity = new UsuarioRastreador();
    $usuario = new Usuario();
    $usuario->setId(2);
    $entity->setUsuario($usuario);
    $rastreador = new Usuario();
    $rastreador->setId(8);
    $entity->setRastreador($rastreador);
    echo "Resultado: " . ServiceLocator::getUsuarioRastreadorService()->create($entity);
}

function testeDelete() {
    echo "Resultado: " . ServiceLocator::getUsuarioRastreadorService()->delete(2);
}

function testeUpdate() {
    $entity = new UsuarioRastreador();
    $entity->setId(8);
    $usuario = new Usuario();
    $usuario->setId(2);
    $entity->setUsuario($usuario);
    $rastreador = new Usuario();
    $rastreador->setId(8);
    $entity->setRastreador($rastreador);
    echo "Resultado: " . ServiceLocator::getUsuarioRastreadorService()->update($entity);
}

function testeReadById() {
    echo "Resultado: " . ServiceLocator::getUsuarioRastreadorService()->readById(19);
}

function testeReadByCriteria() {
    $criteria = array();
    $criteria[UsuarioRastreadorCriteria::USUARIO_FK_EQ] = 7;
    $entityArray = ServiceLocator::getUsuarioRastreadorService()->readByCriteria($criteria);
    foreach ($entityArray as $entity) {
        echo $entity . "<br>";
    }
}
