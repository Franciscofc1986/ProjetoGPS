<?php

include_once realpath(__DIR__) . '/../../model/entity/UsuarioRastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/UsuarioRastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class UsuarioRastreadorService {

    public function create($entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioRastreadorDAO();
            $resultado = $dao->create($conexao, $entity);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

    public function delete($id) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioRastreadorDAO();
            $resultado = $dao->delete($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

    public function readByCriteria($criteria = null, $offset = -1, $limit = -1) {
        $entityArray = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioRastreadorDAO();
            $entityArray = $dao->readByCriteria($conexao, $criteria, $offset, $limit);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $entityArray;
    }

    public function readById($id) {
        $entity = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioRastreadorDAO();
            $entity = $dao->readById($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $entity;
    }

    public function update($entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioRastreadorDAO();
            $resultado = $dao->update($conexao, $entity);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

}
