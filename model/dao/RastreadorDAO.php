<?php

include_once realpath(__DIR__) . '/../../model/entity/Rastreador.php';
include_once realpath(__DIR__) . '/../../model/dao/criteria/RastreadorCriteria.php';

class RastreadorDAO {

    public function create(mysqli $conexao, Rastreador $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "insert into rastreador (serial, nome, data_hora, ultima_coordenada_fk) values (?, ?, ?, ?)";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $ultimaCoordenadaFk = ($entity->getUltimaCoordenada() != null) ? $entity->getUltimaCoordenada()->getId() : NULL;
                $stmt->bind_param("sssi", $entity->getSerial(), $entity->getNome(), $entity->getDataHora(), $ultimaCoordenadaFk);
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
            $sql = "delete from rastreador where id = ?";
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
        $rastreadorArray = array();
        if ($conexao != null) {

            $sql = "select r.id as 'id_r', r.serial, r.nome, r.data_hora as 'data_hora_r', "
                    . "c.id as 'id_c', c.latitude, c.longitude, c.data_hora as 'data_hora_c' "
                    . "from (select * from rastreador where 1=1";

            if (is_array($criteria) && count($criteria) > 0) {
                if (array_key_exists(RastreadorCriteria::ID_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::ID_EQ];
                    if ($aux != null && $aux > 0) {
                        $sql .= " and id = $aux";
                    }
                }
                if (array_key_exists(RastreadorCriteria::SERIAL_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::SERIAL_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and serial = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(RastreadorCriteria::NOME_EQ, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::NOME_EQ];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and nome = '$aux' COLLATE latin1_bin";
                    }
                }
                if (array_key_exists(RastreadorCriteria::NOME_LK, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::NOME_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(nome) like lower('%$aux%')";
                    }
                }
                if (array_key_exists(RastreadorCriteria::DATA_HORA_LK, $criteria)) {
                    $aux = $criteria[RastreadorCriteria::DATA_HORA_LK];
                    if ($aux != null && strlen($aux) > 0) {
                        $sql .= " and lower(data_hora) like lower('%$aux%')";
                    }
                }
            }
            $sql .= ") as r left join (coordenada c) on (r.ultima_coordenada_fk = c.id)";

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
                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['id_r']);
                    $rastreador->setSerial($linha['serial']);
                    $rastreador->setNome($linha['nome']);
                    $rastreador->setDataHora($linha['data_hora_r']);

                    $ultimaCoordenada = new Coordenada();
                    $ultimaCoordenada->setId($linha['id_c']);
                    $ultimaCoordenada->setLatitude($linha['latitude']);
                    $ultimaCoordenada->setLongitude($linha['longitude']);
                    $ultimaCoordenada->setDataHora($linha['data_hora_c']);
                    $ultimaCoordenada->setRastreador($rastreador);
                    $rastreador->setUltimaCoordenada($ultimaCoordenada);

                    $rastreadorArray[] = $rastreador;
                }
            }
            $stmt->close();
        }
        return $rastreadorArray;
    }

    public function readById(mysqli $conexao, $id) {
        $rastreador = null;
        if ($conexao != null && $id > 0) {
            $sql = "select r.id as 'id_r', r.serial, r.nome, r.data_hora as 'data_hora_r', "
                    . "c.id as 'id_c', c.latitude, c.longitude, c.data_hora as 'data_hora_c' "
                    . "from (select * from rastreador where id = ?) as r "
                    . "left join (coordenada c) on (r.ultima_coordenada_fk = c.id)";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($linha = $result->fetch_array(MYSQLI_ASSOC)) {

                    $rastreador = new Rastreador();
                    $rastreador->setId($linha['id_r']);
                    $rastreador->setSerial($linha['serial']);
                    $rastreador->setNome($linha['nome']);
                    $rastreador->setDataHora($linha['data_hora_r']);

                    $ultimaCoordenada = new Coordenada();
                    $ultimaCoordenada->setId($linha['id_c']);
                    $ultimaCoordenada->setLatitude($linha['latitude']);
                    $ultimaCoordenada->setLongitude($linha['longitude']);
                    $ultimaCoordenada->setDataHora($linha['data_hora_c']);
                    $ultimaCoordenada->setRastreador($rastreador);
                    $rastreador->setUltimaCoordenada($ultimaCoordenada);
                }
            }
            $stmt->close();
        }
        return $rastreador;
    }

    public function update(mysqli $conexao, Rastreador $entity) {
        $resultado = false;
        if ($conexao != null && $entity != null) {
            $sql = "update rastreador set serial = ?, nome = ?, data_hora = ?, ultima_coordenada_fk = ? where id = ?";
            $stmt = $conexao->stmt_init();
            if ($stmt->prepare($sql)) {
                $ultimaCoordenadaFk = ($entity->getUltimaCoordenada() != null) ? $entity->getUltimaCoordenada()->getId() : NULL;
                $stmt->bind_param("sssii", $entity->getSerial(), $entity->getNome(), $entity->getDataHora(), $ultimaCoordenadaFk, $entity->getId());
                $resultado = $stmt->execute();
            }
            $stmt->close();
        }
        return $resultado;
    }

}
