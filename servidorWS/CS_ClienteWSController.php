<?php

class ClienteWSController {

    protected $clienteArray;

    public function __construct() {
        $this->clienteArray = array();
    }

    public function getClienteArray() {
        return $this->clienteArray;
    }

    public function setClienteArray($clienteArray) {
        $this->clienteArray = $clienteArray;
    }

    public function getSockets() {
        $sockets = array();
        foreach ($this->clienteArray as $cliente) {
            $sockets[] = $cliente->getSocket();
        }
        return $sockets;
    }

    public function adicionarCliente($cliente) {
        if ($cliente != null) {
            $this->clienteArray[] = $cliente;
            return true;
        }
        return false;
    }

    public function removerCliente($cliente) {
        if ($cliente != null) {
            foreach ($this->clienteArray as $chave => $clienteAux) {
                if ($clienteAux == $cliente) {
                    unset($this->clienteArray[$chave]);
                    return true;
                }
            }
            return true;
        }
        return false;
    }

    public function removerClientePorSocket($socket) {
        if ($socket != null) {
            foreach ($this->clienteArray as $chave => $cliente) {
                if ($cliente->getSocket() == $socket) {
                    unset($this->clienteArray[$chave]);
                    return true;
                }
            }
        }
        return false;
    }

    public function buscarCliente($tipoCliente, $id) {
        if ($tipoCliente != null && $id != null) {
            foreach ($this->clienteArray as $cliente) {
                if ($cliente->getTipoCliente() == $tipoCliente &&
                        $cliente->getId() == $id) {
                    return $cliente;
                }
            }
        }
        return null;
    }

    public function buscarClientePorSocket($socket) {
        if ($socket != null) {
            foreach ($this->clienteArray as $cliente) {
                if ($cliente->getSocket() == $socket) {
                    return $cliente;
                }
            }
        }
        return null;
    }

}
