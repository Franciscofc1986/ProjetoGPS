<?php

include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class RastreadorService {

    public function create($entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new RastreadorDAO();
            $resultado = $dao->create($conexao, $entity);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao->close();
        }
        return $resultado;
    }

    public function delete($id) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new RastreadorDAO();
            $resultado = $dao->delete($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao->close();
        }
        return $resultado;
    }

    public function readByCriteria($criteria = NULL, $offset = -1, $limit = -1) {
        $entityArray = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new RastreadorDAO();
            $entityArray = $dao->readByCriteria($conexao, $criteria, $offset, $limit);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao->close();
        }
        return $entityArray;
    }

    public function readById($id) {
        $entity = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new RastreadorDAO();
            $entity = $dao->readById($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao->close();
        }
        return $entity;
    }

    public function update($entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new RastreadorDAO();
            $resultado = $dao->update($conexao, $entity);
            $conexao->commit();
        } catch (Exception $ex) {
            $conexao->rollback();
            echo $ex->getMessage();
        } finally {
            $conexao->close();
        }
        return $resultado;
    }

}