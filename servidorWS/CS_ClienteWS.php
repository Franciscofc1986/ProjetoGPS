<?php

include_once realpath(__DIR__) . '/../model/service/ServiceLocator.php';

class ClienteWS {

    protected $tipoCliente;
    protected $id;
    protected $socket;
    protected $cadastro;
    protected $tipoComunicacaoAtual;
    protected $socketPar;

    public function __toString() {
        return "$this->tipoCliente# $this->id# $this->tipoComunicacaoAtual";
    }

    public function getTipoCliente() {
        return $this->tipoCliente;
    }

    public function getId() {
        return $this->id;
    }

    public function getSocket() {
        return $this->socket;
    }

    public function getCadastro() {
        return $this->cadastro;
    }

    public function getTipoComunicacaoAtual() {
        return $this->tipoComunicacaoAtual;
    }

    public function getSocketPar() {
        return $this->socketPar;
    }

    public function setTipoCliente($tipoCliente) {
        $this->tipoCliente = $tipoCliente;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSocket($socket) {
        $this->socket = $socket;
    }

    public function setCadastro($cadastro) {
        $this->cadastro = $cadastro;
    }

    public function setTipoComunicacaoAtual($tipoComunicacaoAtual) {
        $this->tipoComunicacaoAtual = $tipoComunicacaoAtual;
    }

    public function setSocketPar($socketPar) {
        $this->socketPar = $socketPar;
    }

    public function atualizarCadastro() {
        if ($this->tipoCliente != null && $this->id > 0) {
            switch ($this->tipoCliente) {
                case TipoCliente::USUARIO:
                    $this->cadastro = ServiceLocator::getUsuarioService()->readById($this->id);
                    break;
                case TipoCliente::RASTREADOR:
                    $this->cadastro = ServiceLocator::getRastreadorService()->readById($this->id);
                    break;
            }
        }
    }

}
