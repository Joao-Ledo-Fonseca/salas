<?php


// remove all session variables
session_unset();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destroy the session
session_destroy();

header("Location: index.php"); // pagina inicial que redireciona para o login via segurança.php
exit();


?>