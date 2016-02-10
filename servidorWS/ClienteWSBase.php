<?php

class ClienteWSBase {

    protected $id;
    protected $socket;

    public function __toString() {
        return "$this->id# $this->socket";
    }

    public function getId() {
        return $this->id;
    }

    public function getSocket() {
        return $this->socket;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSocket($socket) {
        $this->socket = $socket;
    }

}
