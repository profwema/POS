<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../auth.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userlogged'])) {
    http_response_code(401);
    die("Access denied: Please login first.");
}

if (function_exists('auth_init_session')) {
    auth_init_session();
}

if (function_exists('can_view_reports') && !can_view_reports()) {
    http_response_code(403);
    die("Access denied: You do not have permission to view reports.");
}

$server = DB_HOST;
$db = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$version = "0.9d";
$pgport = 5432;
$pchartfolder = "./class/pchart2";

