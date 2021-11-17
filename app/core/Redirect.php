<?php

class Redirect {

    public static function to($location = null) {
        if ($location) {
            if (is_numeric($location)) {
                switch ($location) {
                    case 404:
                        header("HTTP/1.0 404 Not Found");
                        if (file_exists("./inc/404.php")) {
                            include_once "./inc/404.php";
                        } else {
                            include_once "./../inc/404.php";
                        }
                        exit();
                        break;
                }
            }
            header("Location: " . $location);
            exit();
        }
    }
}