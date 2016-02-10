<?php

// CPF 596.156.219-93
//$expressao = '/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/';
//$entrada = '402.698.743-91';
// TELEFONE (35) 3471-9658
//$expressao = '/\([0-9]{2}\) [0-9]{4}-[0-9]{4}/';
//$entrada = '(35) 3471-9654';
// EMAIL joao@hotmail.com
//$expressao = '/[0-9a-zA-Z_.\.]+@[0-9a-zA-Z_.\.]+/';
//$entrada = 'joao@hotmail.com';
// NOME Juliano Costa Silva
//$expressao = '/[a-zA-Zà-úÀ-Ú]+( [a-zA-Zà-úÀ-Ú]+)*/';
//$entrada = 'Juliano Costa Silva';


$expressao = '/(?<=GET \/)[\S]*/';
$entrada = 'GET /SJDHFOANDVA HTTP/1.1';
$matches;

preg_match($expressao, $entrada, $matches);
var_dump($matches);
