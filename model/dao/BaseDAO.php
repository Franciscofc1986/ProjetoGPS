<?php

interface BaseDAO {

    public function create(mysqli $conexao, $entity);

    public function readById(mysqli $conexao, $id);

    public function readByCriteria(mysqli $conexao, $criteria = NULL, $offset = -1, $limit = -1);

    public function update(mysqli $conexao, $entity);

    public function delete(mysqli $conexao, $id);
}
