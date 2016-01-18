<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../../model/dao/CoordenadaDAO.php';

//$coordenada = new Coordenada();
//$coordenada->setLatitude(123);
//$coordenada->setLongitude(321);
//$coordenada->setDataHora(date('Y-m-d H-i-s'));
//$coordenada->setRastreador(2);
//echo testeCreate($coordenada);

//$criteria = array();
//$criteria[CoordenadaCriteria::DATA_HORA_LK] = date('Y-m-d');
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
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
    $dao = new CoordenadaDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    return $entityArray;
}
