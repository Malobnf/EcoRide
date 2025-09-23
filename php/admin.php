<?php
session_start();

// Connexion BDD
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

// Vérification du rôle (admin)
if (!isset($_SESSION['utilisateur_id'])) {
  header('Location: index.php?page=connexion_html'); exit;
}
$st = $pdo->prepare('SELECT nom, role FROM utilisateurs WHERE id = ?');
$st->execute([$_SESSION['utilisateur_id']]);
$user = $st->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['role'] !== 'admin') {
  http_response_code(403);
  echo "Accès refusé."; exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>EcoRide — Admin</title>
  <style>
    .admin-wrap { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
    .cards { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:1rem; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:1rem; }
    .charts { display:grid; gap:1.5rem; margin-top:1.5rem; }
    .chart-box { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:1rem; }
    .muted { color:#666; font-size:.9rem; }
    header { display:flex; align-items:center; justify-content:space-between; padding:1rem 0; }
    .brand a { text-decoration:none; font-weight:700; font-size:1.2rem; }
    .logout { text-decoration:none; }
    canvas { max-height: 340px; }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="admin-wrap">
    <header>
      <div class="brand"><a href="index.php?page=accueil">EcoRide</a></div>
      <nav>
        <a class="logout" href="index.php?page=profil"><i class="fa-solid fa-user"></i> Profil</a>
        &nbsp;|&nbsp;
        <a class="logout" href="index.php?page=deconnexion"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </header>

    <h1>Tableau de bord — Admin</h1>
    <p class="muted">Bonjour <?= htmlspecialchars($user['nom']) ?>. Vue réservée aux administrateurs.</p>

    <section class="cards">
      <div class="card">
        <div class="muted">Crédits cumulés</div>
        <div id="totalCredits" style="font-size:2rem;font-weight:700;">—</div>
      </div>
      <div class="card">
        <div class="muted">Dernier jour (trajets)</div>
        <div id="lastDayTrips" style="font-size:1.6rem;font-weight:700;">—</div>
      </div>
      <div class="card">
        <div class="muted">Dernier mois (trajets)</div>
        <div id="lastMonthTrips" style="font-size:1.6rem;font-weight:700;">—</div>
      </div>
      <div class="card">
        <div class="muted">Dernière année (trajets)</div>
        <div id="lastYearTrips" style="font-size:1.6rem;font-weight:700;">—</div>
      </div>
    </section>

    <section class="charts">
      <div class="chart-box">
        <h3>Trajets par jour</h3>
        <canvas id="chartTripsDay"></canvas>
      </div>
      <div class="chart-box">
        <h3>Trajets par mois</h3>
        <canvas id="chartTripsMonth"></canvas>
      </div>
      <div class="chart-box">
        <h3>Trajets par année</h3>
        <canvas id="chartTripsYear"></canvas>
      </div>
      <div class="chart-box">
        <h3>Crédits par jour</h3>
        <canvas id="chartCreditsDay"></canvas>
      </div>
      <div class="chart-box">
        <h3>Crédits par mois</h3>
        <canvas id="chartCreditsMonth"></canvas>
      </div>
      <div class="chart-box">
        <h3>Crédits par année</h3>
        <canvas id="chartCreditsYear"></canvas>
      </div>
    </section>
  </div>

<script>
(async function(){
  const $ = (s) => document.querySelector(s);
  const resp = await fetch('index.php?page=stats_json');
  if (!resp.ok) {
    alert('Impossible de charger les statistiques ('
      + resp.status + ').'); return;
  }
  const data = await resp.json();
  if (data.error) { alert(data.error); return; }

  // KPIs
  $('#totalCredits').textContent = data.totalCredits ?? '0';
  const last = arr => (Array.isArray(arr) && arr.length ? arr[arr.length-1].total : 0);

  $('#lastDayTrips').textContent   = last(data.trajets.jour);
  $('#lastMonthTrips').textContent = last(data.trajets.mois);
  $('#lastYearTrips').textContent  = last(data.trajets.annee);

  // Créatin d'un graphique simple
  function makeChart(canvasId, labels, values, label) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{ label, data: values, tension: 0.2, fill: false }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true } },
        scales: {
          x: { ticks: { autoSkip: true, maxTicksLimit: 12 } },
          y: { beginAtZero: true, precision: 0 }
        }
      }
    });
  }

  // Mapping des données
  const map = (arr, f) => (Array.isArray(arr) ? arr.map(f) : []);
  makeChart('chartTripsDay',
    map(data.trajets.jour,  x => x.periode),
    map(data.trajets.jour,  x => Number(x.total)),
    'Trajets / jour'
  );
  makeChart('chartTripsMonth',
    map(data.trajets.mois,  x => x.periode),
    map(data.trajets.mois,  x => Number(x.total)),
    'Trajets / mois'
  );
  makeChart('chartTripsYear',
    map(data.trajets.annee, x => x.periode),
    map(data.trajets.annee, x => Number(x.total)),
    'Trajets / an'
  );

  makeChart('chartCreditsDay',
    map(data.credits.jour,  x => x.periode),
    map(data.credits.jour,  x => Number(x.credits)),
    'Crédits / jour'
  );
  makeChart('chartCreditsMonth',
    map(data.credits.mois,  x => x.periode),
    map(data.credits.mois,  x => Number(x.credits)),
    'Crédits / mois'
  );
  makeChart('chartCreditsYear',
    map(data.credits.annee, x => x.periode),
    map(data.credits.annee, x => Number(x.credits)),
    'Crédits / an'
  );
})();
</script>
</body>
</html>



