<?php

if (!function_exists('csrf_token_name')) {
  function csrf_token_name()
  {
    return function_exists('env') ? env('CSRF_TOKEN_NAME', '_csrf') : '_csrf';
  }
}

if (!function_exists('csrf_enabled')) {
  function csrf_enabled()
  {
    $val = function_exists('env') ? env('CSRF_ENABLED', 'true') : 'true';
    return strtolower($val) === 'true' || $val === '1';
  }
}

if (!function_exists('csrf_generate')) {
  function csrf_generate()
  {
    if (!isset($_SESSION)) session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
  }
}

if (!function_exists('csrf_input')) {
  function csrf_input()
  {
    if (!csrf_enabled()) return '';
    $name = htmlspecialchars(csrf_token_name(), ENT_QUOTES, 'UTF-8');
    $token = htmlspecialchars(csrf_generate(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="' . $name . '" value="' . $token . '">';
  }
}

if (!function_exists('csrf_verify')) {
  function csrf_verify()
  {
    if (!csrf_enabled()) return true;
    if (!isset($_SESSION)) session_start();
    $name = csrf_token_name();
    $sent = isset($_POST[$name]) ? (string)$_POST[$name] : '';
    $valid = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
    return $valid;
  }
}
