<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/UsuarioRastreadorDAO.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/UsuarioRastreadorCriteria.php';

// TESTE CREATE
//$entity = new UsuarioRastreador();
//$usuario = new Usuario();
//$usuario->setId(2);
//$entity->setUsuario($usuario);
//$rastreador = new Rastreador();
//$rastreador->setId(10);
//$entity->setRastreador($rastreador);
//echo testeCreate($entity);
// TESTE DELETE
//echo testeDelete(null);
// TESTE UPDATE
//$entity = new UsuarioRastreador();
//$entity->setId(1);
//$usuario = new Usuario();
//$usuario->setId(3);
//$entity->setUsuario($usuario);
//$rastreador = new Rastreador();
//$rastreador->setId(1);
//$entity->setRastreador($rastreador);
//echo testeUpdate($entity);
// TESTE READ BY ID
//echo testeReadById(6);
// TESTE READ BY CRITERIA
//$criteria = array();
//$criteria[UsuarioRastreadorCriteria::RASTREADOR_FK_EQ] = 5;
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new UsuarioRastreadorDAO();
    if (!$dao->create($conexao, $entity)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao = null;
    return $resultado;
}

function testeDelete($id) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new UsuarioRastreadorDAO();
    if (!$dao->delete($conexao, $id)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao = null;
    return $resultado;
}

function testeUpdate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new UsuarioRastreadorDAO();
    if (!$dao->update($conexao, $entity)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao = null;
    return $resultado;
}

function testeReadById($id) {
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new UsuarioRastreadorDAO();
    $entity = $dao->readById($conexao, $id);
    if ($entity == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    $conexao = null;
    return $entity;
}

function testeReadByCriteria($criteria) {
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new UsuarioRastreadorDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    $conexao = null;
    return $entityArray;
}
