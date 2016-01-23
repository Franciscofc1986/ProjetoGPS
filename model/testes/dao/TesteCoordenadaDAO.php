<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/CoordenadaDAO.php';

// TESTE CREATE
//$coordenada = new Coordenada();
//$coordenada->setLatitude(-25.666);
//$coordenada->setLongitude(-25.666);
//$coordenada->setDataHora(date('Y-m-d H-i-s'));
//$coordenada->setHdop(1.23);
//$rastreador = new Rastreador();
//$rastreador->setId(8);
//$coordenada->setRastreador($rastreador);
//echo testeCreate($coordenada);
// TESTE DELETE
//echo testeDelete(11);
// TESTE UPDATE
//$coordenada = new Coordenada();
//$coordenada->setId(6);
//$coordenada->setLatitude(-28);
//$coordenada->setLongitude(-28);
//$coordenada->setDataHora(date('Y-m-d H-i-s'));
//$coordenada->setHdop(0.35);
//$rastreador = new Rastreador();
//$rastreador->setId(8);
//$coordenada->setRastreador($rastreador);
//echo testeUpdate($coordenada);
// TESTE READ BY ID
//echo testeReadById(7);
// TESTE READ BY CRITERIA
//$criteria = array();
//$criteria[CoordenadaCriteria::RASTREADOR_FK_EQ] = 3;
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $conexao->beginTransaction();
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    $conexao = null;
    return $entityArray;
}
