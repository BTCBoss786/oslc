<?php if ($user->isLoggedIn() && $user->hasPermission("admin")) { ?>
    <script src="./../app/js/jquery-3.6.0.min.js"></script>
    <script src="./../app/js/popper.min.js"></script>
    <script src="./../app/js/jquery.dataTables.min.js"></script>
    <script src="./../app/js/dataTables.bootstrap5.min.js"></script>
    <script src="./../app/js/bootstrap.min.js"></script>
    <script src="./js/admin.js"></script>
<?php } else { ?>
    <script src="./app/js/jquery-3.6.0.min.js"></script>
    <script src="./app/js/popper.min.js"></script>
    <script src="./app/js/jquery.dataTables.min.js"></script>
    <script src="./app/js/dataTables.bootstrap5.min.js"></script>
    <script src="./app/js/bootstrap.min.js"></script>
    <script src="<?php echo "./js/" . rtrim(basename($_SERVER["PHP_SELF"]), ".php") . ".js" ?>"></script>
<?php } ?>
</body>
</html>