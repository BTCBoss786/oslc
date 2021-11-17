<?php
require_once "./../app/init.php";
$user = new User();
$user->logout();
Redirect::to("../index.php");