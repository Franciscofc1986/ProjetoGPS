<?php

include_once realpath(__DIR__) . '/../../../model/service/ConnectionManager.php';
include_once realpath(__DIR__) . '/../../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../../model/dao/UsuarioDAO.php';
include_once realpath(__DIR__) . '/../../../model/dao/criteria/UsuarioCriteria.php';

// TESTE CREATE
//$entity = new Usuario();
//$entity->setLogin("JoaoBosco");
//$entity->setSenha("555666");
//$entity->setNome("João Bosco");
//$entity->setDataHora(date('Y-m-d H-i-s'));
//echo testeCreate($entity);
// TESTE DELETE
// echo testeDelete(2);
// TESTE UPDATE
//$entity = new Usuario();
//$entity->setId(6);
//$entity->setLogin("JoaoBosco");
//$entity->setSenha("555666");
//$entity->setNome("João Bosco");
//$entity->setDataHora(date('Y-m-d H-i-s'));
//echo testeUpdate($entity);
// TESTE READ BY ID
//echo testeReadById(6);
// TESTE READ BY CRITERIA
//$criteria = array();
//$criteria[UsuarioCriteria::LOGIN_EQ] = "Creuzinha";
//$criteria[UsuarioCriteria::SENHA_EQ] = "senhaboa";
//$entityArray = testeReadByCriteria($criteria);
//foreach ($entityArray as $entity) {
//    echo "$entity<br>";
//}

function testeCreate($entity) {
    $resultado = false;
    $conexao = ConnectionManager::getConexao();
    $dao = new UsuarioDAO();
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
    $dao = new UsuarioDAO();
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
    $dao = new UsuarioDAO();
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
    $dao = new UsuarioDAO();
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
    $dao = new UsuarioDAO();
    $entityArray = $dao->readByCriteria($conexao, $criteria);
    if ($entityArray == null) {
        $conexao->rollback();
    } else {
        $conexao->commit();
    }
    return $entityArray;
}
