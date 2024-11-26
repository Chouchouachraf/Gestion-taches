<?php
// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=gestion_taches;charset=utf8';
$username = 'root';
$password = ''; // Remplacez par le mot de passe de votre base de données si nécessaire

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Récupération des filtres
$category = $_GET['category'] ?? 'all';
$start = $_GET['period_start'] ?? null;
$end = $_GET['period_end'] ?? null;

// Construction de la requête SQL
$query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS period,
            COUNT(*) AS total_tasks,
            SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_tasks
          FROM tasks
          WHERE 1=1";

$params = [];

if ($category !== 'all') {
    $query .= " AND category = :category";
    $params[':category'] = $category;
}
if ($start) {
    $query .= " AND created_at >= :start";
    $params[':start'] = $start . '-01'; // Ajouter le jour 1
}
if ($end) {
    $query .= " AND created_at <= :end";
    $params[':end'] = $end . '-31'; // Ajouter le dernier jour
}

$query .= " GROUP BY period";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération des données pour le graphique
$periods = [];
$completionRates = [];

foreach ($stats as $stat) {
    $periods[] = $stat['period'];
    $completionRates[] = $stat['completed_tasks'] / $stat['total_tasks'] * 100;
}

// Retourne les données en JSON pour l'appel AJAX
if (isset($_GET['ajax'])) {
    echo json_encode([
        'periods' => $periods,
        'completionRates' => $completionRates
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de Productivité</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        canvas {
            max-width: 100%;
            margin-top: 20px;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Statistiques de Productivité</h1>

    <form id="filterForm">
        <label for="category">Catégorie :</label>
        <select name="category" id="category">
            <option value="all">Toutes les catégories</option>
            <option value="Travail">Travail</option>
            <option value="Personnel">Personnel</option>
        </select>

        <label for="period_start">Début :</label>
        <input type="month" name="period_start" id="period_start">

        <label for="period_end">Fin :</label>
        <input type="month" name="period_end" id="period_end">

        <button type="submit">Filtrer</button>
    </form>

    <canvas id="productivityChart" width="400" height="200"></canvas>

    <script>
        const form = document.getElementById('filterForm');
        const chartCanvas = document.getElementById('productivityChart');
        let chart;

        // Fonction pour charger les données et mettre à jour le graphique
        async function loadChartData() {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const response = await fetch(`statistics.php?ajax=1&${params}`);
            const data = await response.json();

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: data.periods,
                    datasets: [{
                        label: 'Taux de complétion (%)',
                        data: data.completionRates,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Taux de complétion (%)'
                            }
                        }
                    }
                }
            });
        }

        // Charger les données par défaut
        loadChartData();

        // Écouter la soumission du formulaire
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            loadChartData();
        });
    </script>
</body>
</html>
