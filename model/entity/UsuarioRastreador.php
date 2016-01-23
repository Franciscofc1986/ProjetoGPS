<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';

class UsuarioRastreador extends BaseEntity {

    protected $usuario;
    protected $rastreador;

    public function __toString() {
        return "$this->usuario# $this->rastreador";
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getRastreador() {
        return $this->rastreador;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setRastreador($rastreador) {
        $this->rastreador = $rastreador;
    }

}
