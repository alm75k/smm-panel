<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/api.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer le solde actuel via l'API
// Récupérer le solde actuel via l'API
$api = new Api();
$balanceInfo = $api->balance();
$balance = $balanceInfo->balance ?? 0;

// Traitement du formulaire
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    if ($amount > 0) {
        // Ici tu peux intégrer le vrai système de paiement
        // Pour l'exemple, on simule l'ajout
        $balance += $amount;
        $message = "✅ $amount ajouté au solde avec succès !";
    } else {
        $message = "❌ Montant invalide.";
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter des Fonds</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include __DIR__ . '/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">💰 Ajouter des Fonds</h1>

    <?php if($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Solde actuel :</h5>
            <p class="fs-3"><?= number_format($balance, 2) ?> USD</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="amount" class="form-label">Montant à ajouter (USD)</label>
                    <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-success">Ajouter des Fonds</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
