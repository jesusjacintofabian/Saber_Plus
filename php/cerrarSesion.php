<?php
session_start();
// Se borran los datos de la sesión y se destruye
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit;
