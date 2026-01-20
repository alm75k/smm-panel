<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/api.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$filterStatus = $_GET['status'] ?? '';
$filterService = $_GET['service'] ?? '';
$search = $_GET['search'] ?? '';

$servicesStmt = $pdo->query("SELECT service_id, name FROM services ORDER BY name ASC");
$servicesList = $servicesStmt->fetchAll();

$sql = "SELECT o.*, s.name as service_name FROM orders o
        LEFT JOIN services s ON o.service_id = s.service_id
        WHERE 1";

$params = [];

if ($filterStatus) {
    $sql .= " AND o.status = ?";
    $params[] = $filterStatus;
}

if ($filterService) {
    $sql .= " AND o.service_id = ?";
    $params[] = $filterService;
}

if ($search) {
    $sql .= " AND (o.link LIKE ? OR o.user_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include __DIR__ . '/header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4">📋 Historique des Commandes</h1>

        <form class="row g-3 mb-4" method="get">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Recherche lien ou user" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="completed" <?= $filterStatus === 'completed' ? 'selected' : '' ?>>Complété</option>
                    <option value="cancelled" <?= $filterStatus === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="service">
                    <option value="">Tous les services</option>
                    <?php foreach ($servicesList as $s): ?>
                        <option value="<?= $s['service_id'] ?>" <?= $filterService == $s['service_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Service</th>
                        <th>Lien</th>
                        <th>Quantité</th>
                        <th>Prix / Charge</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><?= $o['id'] ?></td>
                                <td><?= $o['user_id'] ?></td>
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
                            <td colspan="8" class="text-center">Aucune commande trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>