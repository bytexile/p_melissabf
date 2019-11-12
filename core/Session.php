<?php

namespace Core;

class Session
{
    // ////////// METODOS
    // - define um ou mais sessoes
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // - retorna uma sessao com a KEY indicada
    public static function get($key)
    {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return false;
    }

    // - destroi uma ou mais sessoes com a KEY indicada
    public static function dell($keys = null)
    {
        if( is_array($keys) ) {
            foreach($keys as $key) {
                unset($_SESSION[$key]);
            }
            
            unset($_SESSION[$keys]);
        }
        else {
            $_SESSION = array();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            session_destroy();
        }
    }
}