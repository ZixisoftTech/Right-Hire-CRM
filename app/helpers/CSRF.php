<?php

namespace App\Helpers;

class CSRF
{
    public static function generateToken()
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function validateToken($token)
    {
        if (!Session::has('csrf_token') || empty($token)) {
            return false;
        }
        return hash_equals(Session::get('csrf_token'), $token);
    }

    public static function getTokenField()
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" id="csrf_token" value="' . $token . '">';
    }
}
