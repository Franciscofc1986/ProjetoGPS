<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/RastreadorCriteria.php';

// TESTE CREATE
//$entity = new Rastreador();
//$entity->setSerial("PQ456");
//$entity->setNome("Rastreador teste");
//$entity->setDataHora(date('Y-m-d H-i-s'));
//echo testeCreate($entity);
// TESTE DELETE
//echo testeDelete(3);
// TESTE UPDATE
//$entity = new Rastreador();
//$entity->setId(8);
//$entity->setSerial("PQ456");
//$entity->setNome("Rastreador teste");
//$entity->setDataHora(date('Y-m-d H-i-s'));
//echo testeUpdate($entity);
// TESTE READ BY ID
//echo testeReadById(6);
// TESTE READ BY CRITERIA
//$criteria = array();
//$criteria[RastreadorCriteria::DATA_HORA_LK] = date('Y-m-d');
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new RastreadorDAO();
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
    $dao = new RastreadorDAO();
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
    $dao = new RastreadorDAO();
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
    $dao = new RastreadorDAO();
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
    $dao = new RastreadorDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao = null;
    }
    return $entityArray;
}
