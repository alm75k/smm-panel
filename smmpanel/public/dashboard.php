<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/api.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$api = new Api();
$balanceInfo = $api->balance();
$balance = $balanceInfo->balance ?? 0;

$totalOrdersStmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$totalOrdersStmt->execute([$userId]);
$totalOrders = $totalOrdersStmt->fetch()['total'] ?? 0;

$pendingStmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'pending'");
$pendingStmt->execute([$userId]);
$pendingOrders = $pendingStmt->fetch()['total'] ?? 0;

$completedStmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'completed'");
$completedStmt->execute([$userId]);
$completedOrders = $completedStmt->fetch()['total'] ?? 0;

$cancelledStmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'cancelled'");
$cancelledStmt->execute([$userId]);
$cancelledOrders = $cancelledStmt->fetch()['total'] ?? 0;
$historyStmt = $pdo->prepare("
    SELECT o.*, s.name as service_name
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.service_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT 5
");
$historyStmt->execute([$userId]);
$recentOrders = $historyStmt->fetchAll();
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include __DIR__ . '/header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4">🏠 Tableau de bord</h1>
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>💰 Solde</h5>
                        <p class="fs-3"><?= number_format($balance, 2) ?> USD</p>
                        <a href="add_funds.php" class="btn btn-sm btn-success">Ajouter des fonds</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>📦 Total Commandes</h5>
                        <p class="fs-3"><?= $totalOrders ?></p>
                        <a href="orders.php" class="btn btn-sm btn-primary">Voir toutes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>⏳ En attente</h5>
                        <p class="fs-4"><?= $pendingOrders ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>✅ Complétées</h5>
                        <p class="fs-4"><?= $completedOrders ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>❌ Annulées</h5>
                        <p class="fs-4"><?= $cancelledOrders ?></p>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="mb-3">🕒 Historique récent</h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Lien</th>
                        <th>Quantité</th>
                        <th>Prix / Charge</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentOrders): ?>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td><?= $o['id'] ?></td>
                                <td><?= htmlspecialchars($o['service_name'] ?? 'N/A') ?></td>
                                <td><a href="<?= htmlspecialchars($o['link']) ?>" target="_blank"><?= htmlspecialchars($o['link']) ?></a></td>
                                <td><?= $o['quantity'] ?></td>
                                <td><?= $o['charge'] ?></td>
                                <td>
                                    <?php
                                    $badge = 'secondary';
                                    if ($o['status'] === 'pending') $badge = 'warning';
                                    if ($o['status'] === 'completed') $badge = 'success';
                                    if ($o['status'] === 'cancelled') $badge = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($o['status']) ?></span>
                                </td>
                                <td><?= $o['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Aucune commande récente</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>