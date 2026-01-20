<?php
require_once 'api.php';
require_once '../config/database.php';

$api = new Api();
$orders = $pdo->query("SELECT id, api_order_id FROM orders")->fetchAll();

foreach ($orders as $o) {
    $s = $api->status($o['api_order_id']);
    $stmt = $pdo->prepare("UPDATE orders SET status=?, remains=? WHERE id=?");
    $stmt->execute([$s['status'], $s['remains'], $o['id']]);
}
