<?php

include_once realpath(__DIR__) . '/../model/service/ServiceLocator.php';

class ClienteWSRastreador extends ClienteWSBase {

    protected $tipoCliente;
    protected $cadastro;
    protected $tipoComunicacaoAtual;
    protected $socketPar;

    public function __toString() {
        return "$this->tipoCliente# $this->id# $this->tipoComunicacaoAtual";
    }

    public function getTipoCliente() {
        return $this->tipoCliente;
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
