<?php

abstract class Servicos {

    static function isJson($string) {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    static function strToHex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0' . $hexCode, -2) . ' ';
        }
        return $hex;
    }

}
