<?php

include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioRastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/dao/UsuarioDAO.php';
include_once realpath(__DIR__) . '/../../model/dao/UsuarioRastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class RastreadorService {

    public function create($entity) {
        $resultado = false;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new RastreadorDAO();
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
            $dao = new RastreadorDAO();
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
            $dao = new RastreadorDAO();
            $entityArray = $dao->readByCriteria($conexao, $criteria, $offset, $limit);
            foreach ($entityArray as $entity) {
                $this->buscarUsuariosDeRastreador($conexao, $entity);
            }
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
            $dao = new RastreadorDAO();
            $entity = $dao->readById($conexao, $id);
            $this->buscarUsuariosDeRastreador($conexao, $entity);
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
            $dao = new RastreadorDAO();
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

    private function buscarUsuariosDeRastreador($conexao, $rastreador) {
        $usuarioArray = array();
        if ($rastreador != null) {
            $criteria = array();
            $criteria[UsuarioRastreadorCriteria::RASTREADOR_FK_EQ] = $rastreador->getId();
            $usuRasDAO = new UsuarioRastreadorDAO();
            $usuRasArray = $usuRasDAO->readByCriteria($conexao, $criteria);
            if (count($usuRasArray) > 0) {
                $usuarioDAO = new UsuarioDAO();
                foreach ($usuRasArray as $usuarioRastreador) {
                    $usuarioArray[] = $usuarioDAO->readById($conexao, $usuarioRastreador->getUsuario()->getId());
                }
                $rastreador->setUsuarioArray($usuarioArray);
            }
        }
        return $usuarioArray;
    }

}
