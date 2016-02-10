<?php

include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/dao/CoordenadaDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class CoordenadaService {

    public function create($coordenada) {
        $resultado = false;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $coordenadaDAO = new CoordenadaDAO();
            if (is_a($coordenada, 'Coordenada')) {
                $resultado = $coordenadaDAO->create($conexao, $coordenada);
            } else if (is_array($coordenada)) {
                $resultado = $coordenadaDAO->createArray($conexao, $coordenada);
            }
            $conexao->commit();
        } catch (Exception $ex) {
            if ($conexao != null) {
                $conexao->rollback();
            }
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

    public function delete($id) {
        $resultado = false;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new CoordenadaDAO();
            $resultado = $dao->delete($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            if ($conexao != null) {
                $conexao->rollback();
            }
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

    public function readByCriteria($criteria = null, $offset = -1, $limit = -1) {
        $entityArray = null;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new CoordenadaDAO();
            $entityArray = $dao->readByCriteria($conexao, $criteria, $offset, $limit);
            $conexao->commit();
        } catch (Exception $ex) {
            if ($conexao != null) {
                $conexao->rollback();
            }
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $entityArray;
    }

    public function readById($id) {
        $entity = null;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new CoordenadaDAO();
            $entity = $dao->readById($conexao, $id);
            $conexao->commit();
        } catch (Exception $ex) {
            if ($conexao != null) {
                $conexao->rollback();
            }
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $entity;
    }

    public function update($entity) {
        $resultado = false;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new CoordenadaDAO();
            $resultado = $dao->update($conexao, $entity);
            $conexao->commit();
        } catch (Exception $ex) {
            if ($conexao != null) {
                $conexao->rollback();
            }
            echo $ex->getMessage();
        } finally {
            $conexao = null;
        }
        return $resultado;
    }

}
