<?php
// controllers/logout.php
session_start();
session_unset();
session_destroy();
setcookie('correo_recordado', '', time() - 3600, "/");
header("Location: ../views/layout/login.php");
exit;
