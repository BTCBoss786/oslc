<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($user->isLoggedIn() && $user->hasPermission("admin")) { ?>
    <link href="./../app/css/bootstrap.min.css" rel="stylesheet">
    <link href="./../app/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="./../app/css/all.min.css" rel="stylesheet">
    <link href="./../css/style.css" rel="stylesheet">
    <?php } else if ($user->isLoggedIn() && $user->hasPermission("user")) { ?>
    <link href="./app/css/bootstrap.min.css" rel="stylesheet">
    <link href="./app/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="./app/css/all.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <?php } else { ?>
    <link href="./app/css/bootstrap.min.css" rel="stylesheet">
    <link href="./app/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="./app/css/all.min.css" rel="stylesheet">
    <link href="./css/index.css" rel="stylesheet">
    <?php } ?>
    <title>Office Solution for Labour Contractor</title>
</head>
<body>