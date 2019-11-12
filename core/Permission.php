<?php

namespace Core;

class Permission
{
    private static $email = null;

    public function __construct()
    {
        if(Session::get('user')){
            $user = Session::get('user');

            self::$email = $user['email'];
        }
    }

    public static function email()
    {
        return self::$email;
    }

    public static function check()
    {
        if(self::$email == null) {
            return false;
        }
        return true;
    }
}