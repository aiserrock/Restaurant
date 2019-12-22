<?php if (!defined('security_hash')) {
    die("Недостаточно прав");
}
session_destroy();
header('Location: /login.php');

