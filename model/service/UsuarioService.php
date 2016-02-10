<?php

include_once realpath(__DIR__) . '/../../model/entity/Usuario.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/UsuarioRastreadorCriteria.php';
include_once realpath(__DIR__) . '/../../model/dao/UsuarioDAO.php';
include_once realpath(__DIR__) . '/../../model/dao/RastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/dao/UsuarioRastreadorDAO.php';
include_once realpath(__DIR__) . '/../../model/service/ConnectionManager.php';

class UsuarioService {

    public function create($entity) {
        $resultado = false;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioDAO();
            $resultado = $dao->create($conexao, $entity);
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
            $dao = new UsuarioDAO();
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
            $dao = new UsuarioDAO();
            $entityArray = $dao->readByCriteria($conexao, $criteria, $offset, $limit);
            foreach ($entityArray as $entity) {
                $this->buscarRastreadoresDeUsuario($conexao, $entity);
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
        return $entityArray;
    }

    public function readById($id) {
        $entity = null;
        $conexao = null;
        try {
            $conexao = ConnectionManager::getConexao();
            $conexao->beginTransaction();
            $dao = new UsuarioDAO();
            $entity = $dao->readById($conexao, $id);
            $this->buscarRastreadoresDeUsuario($conexao, $entity);
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
            $dao = new UsuarioDAO();
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

    private function buscarRastreadoresDeUsuario($conexao, $usuario) {
        $rastreadorArray = array();
        if ($usuario != null) {
            $criteria = array();
            $criteria[UsuarioRastreadorCriteria::USUARIO_FK_EQ] = $usuario->getId();
            $usuRasDAO = new UsuarioRastreadorDAO();
            $usuRasArray = $usuRasDAO->readByCriteria($conexao, $criteria);
            if (count($usuRasArray) > 0) {
                $rastreadorDAO = new RastreadorDAO();
                foreach ($usuRasArray as $usuarioRastreador) {
                    $rastreadorArray[] = $rastreadorDAO->readById($conexao, $usuarioRastreador->getRastreador()->getId());
                }
                $usuario->setRastreadorArray($rastreadorArray);
            }
        }
        return $rastreadorArray;
    }

}
