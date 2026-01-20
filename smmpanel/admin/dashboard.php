<?php
require_once '../core/auth.php';
auth_required();
if (!is_admin($pdo)) die('403');

$stats = $pdo->query("
  SELECT COUNT(*) users,
         (SELECT COUNT(*) FROM orders) orders,
         (SELECT SUM(balance) FROM user_balances) money
")->fetch();

print_r($stats);
