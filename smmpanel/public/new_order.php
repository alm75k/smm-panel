<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$servicesStmt = $pdo->query("SELECT * FROM services ORDER BY name ASC");
$servicesList = $servicesStmt->fetchAll();

$selectedServiceId = intval($_GET['service_id'] ?? 0);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = intval($_POST['service_id']);
    $quantity = intval($_POST['quantity']);
    $link = $_POST['link'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();

    if (!$service) {
        $message = "❌ Service invalide.";
    } elseif ($quantity <= 0) {
        $message = "❌ Quantité invalide.";
    } else {

        $total = ($service['rate'] / 1000) * $quantity;

        if ($balance < $total) {
            $message = "❌ Fonds insuffisants. Solde actuel : $balance USD, coût : $total USD";
        } else {
            $orderResult = $api->order([
                'service' => $serviceId,
                'link' => $link,
                'quantity' => $quantity
            ]);

            $stmt2 = $pdo->prepare("
                INSERT INTO orders (user_id, api_order_id, service_id, status, link, quantity, charge, start_count, remains, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt2->execute([
                $userId,
                $orderResult->order ?? 0,
                $serviceId,
                $orderResult->status ?? 'pending',
                $link,
                $quantity,
                $total,
                $orderResult->start_count ?? 0,
                $orderResult->remains ?? 0
            ]);

            $message = "✅ Commande passée avec succès ! Total : $total USD";
            $balance -= $total;
        }
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include __DIR__ . '/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">🛒 Nouvelle Commande</h1>

    <?php if($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" id="orderForm">
                <div class="mb-3">
                    <label for="service_id" class="form-label">Service</label>
                    <select class="form-select" id="service_id" name="service_id" required>
                        <option value="">-- Sélectionner un service --</option>
                        <?php foreach($servicesList as $s): ?>
                            <option value="<?= $s['service_id'] ?>" <?= $s['service_id']==$selectedServiceId?'selected':'' ?>>
                                <?= htmlspecialchars($s['name']) ?> (<?= $s['rate'] ?> USD / 1000)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="link" class="form-label">Lien</label>
                    <input type="url" class="form-control" id="link" name="link" required placeholder="https://example.com">
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantité</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="100" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total</label>
                    <p class="fs-5" id="totalDisplay">0 USD</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Solde actuel</label>
                    <p class="fs-5" id="balanceDisplay"><?= number_format($balance,2) ?> USD</p>
                </div>

                <button type="submit" class="btn btn-success">Commander</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const serviceSelect = document.getElementById('service_id');
const quantityInput = document.getElementById('quantity');
const totalDisplay = document.getElementById('totalDisplay');
const balanceDisplay = document.getElementById('balanceDisplay');

const rates = {};
<?php foreach($servicesList as $s): ?>
rates[<?= $s['service_id'] ?>] = <?= $s['rate'] ?>;
<?php endforeach; ?>

function updateTotal() {
    const serviceId = parseInt(serviceSelect.value);
    const quantity = parseInt(quantityInput.value) || 0;
    if(serviceId && rates[serviceId]) {
        const total = (rates[serviceId]/1000) * quantity;
        totalDisplay.textContent = total.toFixed(2) + " USD";
    } else {
        totalDisplay.textContent = "0 USD";
    }
}

serviceSelect.addEventListener('change', updateTotal);
quantityInput.addEventListener('input', updateTotal);
window.addEventListener('load', updateTotal);
</script>

</body>
</html>
