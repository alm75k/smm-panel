<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/api.php';

try {
    $api = new Api();
    $allServices = $api->services() ?? [];

    foreach($allServices as $s){
        $s = is_object($s) ? $s : (object)$s;

        $stmt = $pdo->prepare("
            REPLACE INTO services 
            (service_id, name, category, type, rate, min, max, dripfeed, refill, cancel, created_at, currency)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ");

        $stmt->execute([
            $s->service,
            $s->name,
            $s->category ?? '',
            $s->type ?? '',
            isset($s->rate) ? (float)$s->rate : 0,
            isset($s->min) ? (int)$s->min : 0,
            isset($s->max) ? (int)$s->max : 0,
            isset($s->dripfeed) ? (int)$s->dripfeed : 0,
            isset($s->refill) ? (int)$s->refill : 0,
            isset($s->cancel) ? (int)$s->cancel : 0,
            $s->currency ?? 'USD'
        ]);
    }

    echo "✅ Synchronisation terminée. " . count($allServices) . " services mis à jour.";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
