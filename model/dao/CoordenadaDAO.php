<?php

include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/CoordenadaCriteria.php';

class CoordenadaDAO {

    public function create(mysqli $conexao, Coordenada $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "insert into coordenada (latitude, longitude, data_hora, rastreador_fk) values (?, ?, ?, ?)";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $rastreadorFk = ($entity->getRastreador() != null) ? $entity->getRastreador()->getId() : NULL;
                $stmt->bind_param("ddsi", $entity->getLatitude(), $entity->getLongitude(), $entity->getDataHora(), $rastreadorFk);
                $resultado = $stmt->execute();
                $entity->setId($stmt->insert_id);
            }
            $stmt->close();
        }
        return $resultado;
    }

    public function delete(mysqli $conexao, $id) {
        $resultado = false;
        if ($conexao != null && $id > 0) {
            $sql = "delete from coordenada where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("i", $id);
                $resultado = $stmt->execute();
            }
            $stmt->close();
        }
        return $resultado;
    }

    public function readByCriteria(mysqli $conexao, $criteria = NULL, $offset = -1, $limit = -1) {
        $entityArray = array();
        if ($conexao != null) {

            $sql = "select * from coordenada where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(CoordenadaCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(CoordenadaCriteria::DATA_HORA_LK, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::DATA_HORA_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(data_hora) like lower('%$aux%')";
                    }
                }
                if (array_key_exists(CoordenadaCriteria::RASTREADOR_EQ, $criteria)) {
                    $aux = $criteria[CoordenadaCriteria::RASTREADOR_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and rastreador_fk = $aux";
                    }
                }
            }

            if ($limit > 0) {
                $sql .= " limit $limit";
            }
            if ($offset > 0) {
                $sql .= " offset $offset";
            }

            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($linha = $result->fetch_array(MYSQLI_ASSOC)) {
                    $entity = new Coordenada();
                    $entity->setId($linha['id']);
                    $entity->setLatitude($linha['latitude']);
                    $entity->setLongitude($linha['longitude']);
                    $entity->setDataHora($linha['data_hora']);

                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['rastreador_fk']);
                    $entity->setRastreador($rastreador);

                    $entityArray[] = $entity;
                }
            }
            $stmt->close();
        }
        return $entityArray;
    }

    public function readById(mysqli $conexao, $id) {
        $entity = null;
        if ($conexao != null && $id > 0) {
            $sql = "select * from coordenada where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($linha = $result->fetch_array(MYSQLI_ASSOC)) {
                    $entity = new Coordenada();
                    $entity->setId($linha['id']);
                    $entity->setLatitude($linha['latitude']);
                    $entity->setLongitude($linha['longitude']);
                    $entity->setDataHora($linha['data_hora']);

                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['rastreador_fk']);
                    $entity->setRastreador($rastreador);
                }
            }
            $stmt->close();
        }
        return $entity;
    }

    public function update(mysqli $conexao, Coordenada $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "update coordenada set latitude = ?, longitude = ?, data_hora = ?, rastreador_fk = ? where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $rastreadorFk = ($entity->getRastreador() != null) ? $entity->getRastreador()->getId() : NULL;
                $stmt->bind_param("ddsii", $entity->getLatitude(), $entity->getLongitude(), $entity->getDataHora(), $rastreadorFk, $entity->getId());
                $resultado = $stmt->execute();
            }
            $stmt->close();
        }
        return $resultado;
    }

}
