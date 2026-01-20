<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/api.php';
auth_required();

$api = new Api();
$allServices = $api->services() ?? [];
$allServices = array_map(function ($s) {
    return is_object($s) ? $s : (object)$s;
}, $allServices);

$categories = [];
foreach ($allServices as $s) {
    if (!empty($s->category) && !in_array($s->category, $categories)) {
        $categories[] = $s->category;
    }
}

$filterCategory = $_GET['category'] ?? '';
if ($filterCategory) {
    $allServices = array_filter($allServices, function ($s) use ($filterCategory) {
        return $s->category === $filterCategory;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Services - SMM Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="container mt-4">
        <h2>Liste des services</h2>

        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un service...">
            </div>
            <div class="col-md-6">
                <select id="categoryFilter" class="form-select">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($cat === $filterCategory) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="table table-striped table-bordered" id="servicesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allServices as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s->service ?? '') ?></td>
                        <td><?= htmlspecialchars($s->name ?? '') ?></td>
                        <td><?= htmlspecialchars($s->category ?? '') ?></td>
                        <td><?= htmlspecialchars($s->rate ?? '') ?> <?= htmlspecialchars($s->currency ?? '') ?> per 1000</td>
                        <td><?= htmlspecialchars($s->description ?? '') ?></td>
                        <td>
                            <a href="new_order.php?service_id=1" class="btn btn-primary btn-sm">Commander</a>


                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($allServices)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun service disponible</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#servicesTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $('#categoryFilter').on('change', function() {
                var category = $(this).val();
                if (category) {
                    $('#servicesTable tbody tr').filter(function() {
                        $(this).toggle($(this).find('td:nth-child(3)').text() === category);
                    });
                } else {
                    $('#servicesTable tbody tr').show();
                }
            });
        });
    </script>
</body>

</html>