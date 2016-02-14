<?php

class ClienteWSControllerRastreador extends ClienteWSControllerBase {

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

    public function buscarClientePorCadastro($cadastro) {
        if ($cadastro != null) {
            foreach ($this->clienteArray as $cliente) {
                if ($cadastro->comparar($cliente->getCadastro())) {
                    return $cliente;
                }
            }
        }
        return null;
    }

}
