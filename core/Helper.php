<?php

namespace Core;

class Helper
{
    // ----- Retorna o IP do usuario
    public static function getUserIP()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }


    // ----- Retorna dados de localisação do usuario
    public static function getLocation()
    {
        // Helper::getUserIP();
        $ip = $_SERVER['REMOTE_ADDR'];

        // $getLocation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=177.143.207.220'));
        $getLocation = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip) );

        $location = new \stdClass;

        // $location->ip_request = $getLocation['geoplugin_request'];
        $location->city   = $getLocation['geoplugin_city'];
        $location->regiao = $getLocation['geoplugin_regionCode'];
        $location->pais   = $getLocation['geoplugin_countryName'];
        $location->lat    = $getLocation['geoplugin_latitude'];
        $location->log    = $getLocation['geoplugin_longitude'];

        return $location;
    }


    // ----- Setando um COOKIE
    public static function setCookie($key, $value)
    {
        setcookie($key, $value, time() + (86400 * 30), '/'); // 86400 = 1 day
    }

    // ----- Apagando um COOKIE
    public static function dellCookie($key)
    {
        if( is_array($key) ){
            foreach($key as $element) {
                unset($_COOKIE[$element]);
            }
        }
        else {
            unset($_COOKIE[$key]);
        }
    }

    public static function validaCPF($cpf = null)
    {
        // Verifica se um número foi informado
        if(empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' || 
                 $cpf == '11111111111' || 
                 $cpf == '22222222222' || 
                 $cpf == '33333333333' || 
                 $cpf == '44444444444' || 
                 $cpf == '55555555555' || 
                 $cpf == '66666666666' || 
                 $cpf == '77777777777' || 
                 $cpf == '88888888888' || 
                 $cpf == '99999999999') {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        }

        else {   
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}