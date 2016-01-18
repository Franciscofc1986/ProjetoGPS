<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/RastreadorCriteria.php';

//$entity = new Rastreador();
//$entity->setSerial("askf9k3424304");
//$entity->setNome("Rastreador teste 2");
//$entity->setDataHora(date('Y-m-d H-i-s'));
//echo testeCreate($entity);

//$criteria = array();
//$criteria[RastreadorCriteria::DATA_HORA_LK] = date('Y-m-d');
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $dao = new RastreadorDAO();
    if (!$dao->create($conexao, $entity)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao->close();
    return $resultado;
}

function testeDelete($id) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $dao = new RastreadorDAO();
    if (!$dao->delete($conexao, $id)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao->close();
    return $resultado;
}

function testeUpdate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $dao = new CoordenadaDAO();
    if (!$dao->update($conexao, $entity)) {
        $conexao->rollback();
    } else {
        $conexao->commit();
        $resultado = true;
    }
    $conexao->close();
    return $resultado;
}

function testeReadById($id) {
    $conexao = ConnectionManager::getConexao();
    $dao = new RastreadorDAO();
    $entity = $dao->readById($conexao, $id);
    if ($entity == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    $conexao->close();
    return $entity;
}

function testeReadByCriteria($criteria) {
    $conexao = ConnectionManager::getConexao();
    $dao = new RastreadorDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    return $entityArray;
}
