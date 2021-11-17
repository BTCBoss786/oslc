<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("signIn") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "username" => [
            "name" => "Username",
            "required" => true
        ],
        "password" => [
            "name" => "Password",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $user = new User();
        $remember = (Input::get("remember")) ? true : false;
        $login = $user->login(Input::get("username"), Input::get("password"), $remember);
        if ($login) {
            $response["status"] = true;
            $response["data"][] = "Sign In Successful";
            if ($user->hasPermission("admin"))
                Redirect::to("./../admin/dashboard.php");
            Redirect::to("./../dashboard.php");
        } else {
            $response["data"][] = "Invalid Username or Password";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("signInResponse", json_encode($response));
    Redirect::to("./../index.php#signInDiv");
}

if (Input::get("resetPassword") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "username" => [
            "name" => "Username",
            "required" => true
        ],
        "secret" => [
            "name" => "Secret Code",
            "required" => true,
            "min" => 4,
            "max" => 4
        ],
        "password" => [
            "name" => "Password",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $user = new User(Input::get('username'));
        if ($user->data()->Secret === Input::get("secret")) {
            $salt = Hash::salt(32);
            $user->update([
                "Password" => Hash::make(Input::get("password"), $salt),
                "Salt" => $salt
            ], $user->data()->UserId);
            $response["status"] = true;
            $response["data"][] = "Password Reset Successfully";
            Session::flash("signInResponse", json_encode($response));
            Redirect::to("./../index.php");
        } else {
            $response["data"][] = "Invalid Username or Secret Code";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("resetPasswordResponse", json_encode($response));
    Redirect::to("./../index.php#resetPasswordDiv");
}

if (Input::get("changePassword") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "oldPassword" => [
            "name" => "Old Password",
            "required" => true
        ],
        "newPassword" => [
            "name" => "New Password",
            "required" => true,
            "min" => 4
        ],
        "confirmPassword" => [
            "name" => "Confirm Password",
            "required" => true,
            "match" => "newPassword"
        ]
    ]);
    if ($validation->passed()) {
        $user = new User();
        if ($user->data()->Password === Hash::make(Input::get("oldPassword"), $user->data()->Salt)) {
            $salt = Hash::salt(32);
            $user->update([
                "Password" => Hash::make(Input::get("newPassword"), $salt),
                "Salt" => $salt
            ]);
            $response["status"] = true;
            $response["data"][] = "Password Changed Successfully";
        } else {
            $response["data"][] = "Entered Wrong Old Password";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("changePasswordResponse", json_encode($response));
    if ($user->hasPermission("admin"))
        Redirect::to("./../admin/changepassword.php");
    Redirect::to("./../changepassword.php");
}

if (Input::get("updateProfile") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "fullName" => [
            "name" => "Full Name",
            "required" => true,
            "min" => 3

        ],
        "secret" => [
            "name" => "Secret Code",
            "required" => true,
            "min" => 4,
            "max" => 4
        ]
    ]);
    if ($validation->passed()) {
        $user = new User();
        if ($user->isLoggedIn()) {
            $user->update([
                "Fullname" => Input::get("fullName"),
                "Secret" => Input::get("secret")
            ]);
            $response["status"] = true;
            $response["data"][] = "Profile Updated Successfully";
        } else {
            $response["data"][] = "Profile Update Failed";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("profileResponse", json_encode($response));
    if ($user->hasPermission("admin"))
        Redirect::to("./../admin/profile.php");
    Redirect::to("./../profile.php");
}

if (Input::get("addUser") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "fullName" => [
            "name" => "Full Name",
            "required" => true,
            "min" => 3
        ],
        "userName" => [
            "name" => "Username",
            "required" => true,
            "min" => 3
        ],
        "password" => [
            "name" => "Password",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $user = new User();
        $salt = Hash::salt(32);
        $user->create([
            "Username" => Input::get("userName"),
            "Password" => Hash::make(Input::get("password"), $salt),
            "Salt" => $salt,
            "Secret" => Hash::unique(4),
            "GroupId" => Input::get("groupId"),
            "FullName" => Input::get("fullName")
        ]);
        $response["status"] = true;
        $response["data"][] = "User Created Successfully";
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("userResponse", json_encode($response));
    Redirect::to("./../admin/user.php");
}

if (Input::get("deleteUser") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "userId" => [
            "name" => "User ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $user = new User();
        if ($user->data()->UserId != Input::get("userId") && $user->delete(Input::get("userId"))) {
            $response["status"] = true;
            $response["data"][] = "User Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete User";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("userResponse", json_encode($response));
    Redirect::to("./../admin/user.php");
}

if (Input::get("fetch")) {
    $user = new User();
    $data = $user->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}