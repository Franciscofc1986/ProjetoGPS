<?php

include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/CoordenadaDAO.php';
include_once realpath(__DIR__) . '/../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class CoordenadaService {

    public function create(Coordenada $coordenada) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $coordenadaDAO = new CoordenadaDAO();
            if ($coordenadaDAO->create($conexao, $coordenada)) {
                $rastreadorDAO = new RastreadorDAO();
                $rastreador = $rastreadorDAO->readById($conexao, $coordenada->getRastreador()->getId());
                if ($rastreador != null) {
                    $rastreador->setUltimaCoordenada($coordenada);
                    $resultado = $rastreadorDAO->update($conexao, $rastreador);
                }
            }
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
            $dao = new CoordenadaDAO();
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
            $dao = new CoordenadaDAO();
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
            $dao = new CoordenadaDAO();
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

    public function update(Coordenada $entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $dao = new CoordenadaDAO();
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
