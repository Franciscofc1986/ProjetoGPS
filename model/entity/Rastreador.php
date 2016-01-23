<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';

class Rastreador extends BaseEntity {

    protected $serial;
    protected $nome;
    protected $publico;
    protected $ultimaCoordenada;
    protected $usuarioArray;
    protected $coordenadaArray;

    public function __construct() {
        $this->usuarioArray = array();
        $this->coordenadaArray = array();
    }

    public function __toString() {
        return "$this->id# $this->serial# $this->nome# $this->publico# $this->ultimaCoordenada";
    }

    public function getSerial() {
        return $this->serial;
    }

    public function getNome() {
        return $this->nome;
    }

    public function isPublico() {
        return $this->publico;
    }

    public function getUltimaCoordenada() {
        return $this->ultimaCoordenada;
    }

    public function getUsuarioArray() {
        return $this->usuarioArray;
    }

    public function getCoordenadaArray() {
        return $this->coordenadaArray;
    }

    public function setSerial($serial) {
        $this->serial = $serial;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setPublico($publico) {
        $this->publico = $publico;
    }

    public function setUltimaCoordenada(Coordenada $ultimaCoordenada) {
        $this->ultimaCoordenada = $ultimaCoordenada;
    }

    public function setUsuarioArray($usuarioArray) {
        $this->usuarioArray = $usuarioArray;
    }

    public function setCoordenadaArray($coordenadaArray) {
        $this->coordenadaArray = $coordenadaArray;
    }

}
