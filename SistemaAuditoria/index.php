<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: assets/pages/dashboard.php");
} else {
    header("Location: assets/pages/login.php");
}
exit;
?>