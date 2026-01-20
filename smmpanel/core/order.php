<?php
require_once __DIR__ . '/../config/database.php'; // doit pointer sur le fichier correct
require_once __DIR__ . '/api.php';


function createOrder($userId, $serviceId, $link, $quantity)
{
    global $pdo;

    $api = new Api();
    $allServices = $api->services() ?? [];
    $service = null;

    foreach ($allServices as $s) {
        $s = is_object($s) ? $s : (object)$s;
        if (($s->service ?? '') == $serviceId) {
            $service = $s;
            break;
        }
    }

    if (!$service) {
        throw new Exception("Service introuvable");
    }

    $totalPrice = ($quantity * $service->rate) / 1000;

    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, service_id, link, quantity, status, charge, start_count, remains, currency, error)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $serviceId,
        $link,
        $quantity,
        'pending',
        $totalPrice,
        0,
        $quantity,
        $service->currency ?? 'USD',
        null
    ]);
}
