<?php

interface BaseService {

    public function create($entity);

    public function readById($id);

    public function readByCriteria($criteria = NULL, $offset = -1, $limit = -1);

    public function update($entity);

    public function delete($id);
}
