<?php

class Hash {

    public static function make($string, $salt = "") {
        return hash("sha256", $string . $salt);
    }

    public static function salt($length) {
        return bin2hex(random_bytes($length));
    }

    public static function unique($length = null) {
        if ($length) {
            return str_pad(rand(0, str_repeat(9, $length)), 4, STR_PAD_LEFT);
        }
        return self::make(uniqid());
    }
}