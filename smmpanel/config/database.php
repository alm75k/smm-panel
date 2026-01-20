<?php
/**
 * Configuration PDO pour la connexion à la base de données
 * Compatible MAMP/NGINX sous Windows
 */

$host = '127.0.0.1';      // Adresse du serveur MySQL
$db   = 'smm_panel';       // Nom de la base de données
$user = 'root';           // Utilisateur MySQL (souvent 'root' sur MAMP)
$pass = 'root';           // Mot de passe MySQL (souvent 'root' sur MAMP)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lancer exceptions sur erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourner des tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Utiliser les vrais prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Affiche une erreur plus lisible pour le dev
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
