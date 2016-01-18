<?php

include_once realpath(__DIR__) . '/../../model/entity/BaseEntity.php';
include_once realpath(__DIR__) . '/../../model/entity/Coordenada.php';

class Rastreador extends BaseEntity {

    protected $serial;
    protected $nome;
    protected $dataHora;
    protected $ultimaCoordenada;
    protected $usuarioArray;

    public function __toString() {
        return "$this->id# $this->serial# $this->nome# $this->dataHora# $this->ultimaCoordenada";
    }

    public function getSerial() {
        return $this->serial;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDataHora() {
        return $this->dataHora;
    }

    public function getUltimaCoordenada() {
        return $this->ultimaCoordenada;
    }

    public function getUsuarioArray() {
        return $this->usuarioArray;
    }

    public function setSerial($serial) {
        $this->serial = $serial;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setDataHora($dataHora) {
        $this->dataHora = $dataHora;
    }

    public function setUltimaCoordenada(Coordenada $ultimaCoordenada) {
        $this->ultimaCoordenada = $ultimaCoordenada;
    }

    public function setUsuarioArray($usuarioArray) {
        $this->usuarioArray = $usuarioArray;
    }

}
