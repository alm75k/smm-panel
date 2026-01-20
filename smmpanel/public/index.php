<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
} else {
    header('Location: /auth/login.php');
}
exit;
