<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Comparavel.php';

class UsuarioRastreador extends BaseEntity implements Comparavel {

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

    public function comparar($objeto) {
        if (!is_a($objeto, __CLASS__) ||
                $this->id !== $objeto->getId()) {
            return false;
        }
        if ($this->usuario == null || $objeto->getUsuario() == null) {
            if ($this->usuario != $objeto->getUsuario()) {
                return false;
            }
        } else {
            if ($this->usuario->comparar($objeto->getUsuario()) === false) {
                return false;
            }
        }
        if ($this->rastreador == null || $objeto->getRastreador() == null) {
            if ($this->rastreador != $objeto->getRastreador()) {
                return false;
            }
        } else {
            if ($this->rastreador->comparar($objeto->getRastreador()) === false) {
                return false;
            }
        }
        return true;
    }

}
