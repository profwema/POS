<?php

// RBAC بسيط قائم على جلسة المستخدم

if (!function_exists('current_user_id')) {
  function current_user_id()
  {
    return isset($_SESSION['userlogged']) ? (int)$_SESSION['userlogged'] : 0;
  }
}

if (!function_exists('current_user_role')) {
  function current_user_role()
  {
    // يفترض أن الدور مخزن في الجلسة مثلاً: admin, accountant, cashier, storekeeper
    return isset($_SESSION['role']) ? (string)$_SESSION['role'] : 'guest';
  }
}

if (!function_exists('require_role')) {
  function require_role($roles)
  {
    if (!is_array($roles)) $roles = [$roles];
    $role = current_user_role();
    if (!in_array($role, $roles, true)) {
      http_response_code(403);
      echo 'Access denied';
      exit;
    }
  }
}

