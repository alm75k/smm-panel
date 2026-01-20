<?php
function auth_required() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}
