<?php
session_start();

// Clear Session
session_unset();
session_destroy();

// Delete Cookie
if (isset($_COOKIE['medicare_user'])) {
    setcookie('medicare_user', '', time() - 3600, '/');
}

header("Location: login.php");
exit();
?>