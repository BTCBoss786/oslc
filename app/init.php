<?php
session_start();

$GLOBALS["config"] = [
    "mysql" => [
        "host" => "127.0.0.1",
        "user" => "root",
        "pass" => "",
        "db" => "oslc"
    ],
    "cookie" => [
        "name" => "hash",
        "expiry" => 604800
    ],
    "session" => [
        "name" => "user",
        "token" => "token"
    ]
];

spl_autoload_register(function ($class) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/oslc/app/core/{$class}.php")) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/oslc/app/core/{$class}.php";
    } else {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/oslc/cls/{$class}.php";
    }
});

if (Cookie::exists(Config::get("cookie/name")) && !Session::exists(Config::get("session/name"))) {
    $hash = Cookie::get(Config::get("cookie/name"));
    $hashCheck = DB::getInstance()->get("Users_Session", ["Hash", "=", $hash]);
    if ($hashCheck) {
        $user = new User($hashCheck->first()->UserId);
        $user->login();
    }
}