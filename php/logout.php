<?php
session_start();

// 1. Unset all session variables
session_unset();

// 2. Destroy the session completely
session_destroy();

// 3. Redirect to Login Page
header("Location: login.php");
exit();
?>