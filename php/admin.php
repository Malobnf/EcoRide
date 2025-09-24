<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['utilisateur_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  header('Location: index.php?page=connexion_html');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoRide • Tableau de bord</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .container { max-width: 1200px; margin: 0 auto; padding: 1rem; }
    .tabs { display:flex; gap:.5rem; margin:1rem 0; flex-wrap:wrap; }
    .tabs button { padding:.5rem .9rem; border:1px solid #cbd5e1; background:#fff; cursor:pointer; border-radius:.5rem; }
    .tabs button.active { background:#0f766e; border-color:#0f766e; color:#fff; }
    .tab-content { display:none; }
    .tab-content.active { display:block; }
    .subtabs { display:flex; gap:.5rem; margin:.5rem 0 1rem; flex-wrap:wrap; }
    .subtabs button { padding:.4rem .7rem; border:1px solid #cbd5e1; background:#fff; border-radius:.5rem; cursor:pointer; }
    .subtabs button.active { background:#334155; border-color:#334155; color:#fff; }
    .grid { display:grid; gap:1rem; }
    .grid-2 { grid-template-columns: 1fr 1fr; }
    @media (max-width: 1000px){ .grid-2 { grid-template-columns: 1fr; } }
    .card { border:1px solid #e5e7eb; border-radius:.75rem; padding:1rem; background:#fff; }
    .row { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .table { width:100%; border-collapse:collapse; }
    .table th, .table td { border-bottom:1px solid #e5e7eb; padding:.5rem; text-align:left; }
    .btn { padding:.35rem .6rem; border:1px solid #d1d5db; background:#fff; cursor:pointer; border-radius:.35rem; }
    .btn.primary { background:#0f766e; border-color:#0f766e; color:#fff; }
    .btn.danger { background:#b91c1c; border-color:#b91c1c; color:#fff; }
    .btn.small { font-size:.9rem; padding:.25rem .5rem; }
    .hidden { display:none; }
    .pill { display:inline-block; padding:.15rem .5rem; border-radius:999px; font-size:.8rem; background:#f1f5f9; }
    .kpi { font-size: 1.1rem; }
    .select { padding:.35rem .5rem; border:1px solid #cbd5e1; border-radius:.35rem; background:#fff; }
    .chart-wrap { position: relative; width: 100%; height: 320px; }
    @media (max-width: 600px){ .chart-wrap { height: 260px; } }
    .chart-wrap > canvas { position: absolute; inset: 0; }
    .card canvas { height: auto !important; }
  </style>
</head>
<body>
  <header>
    <div class="deroulant">☰</div>
    <div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
    <nav class="side-menu" id="sideMenu">
      <a href="index.php?page=accueil">Accueil</a>
      <a class="current-page">Dashboard</a>
      <a href="index.php?page=profil">Profil</a>
      <a href="index.php?page=contact">Contact</a>
    </nav>
  </header>

  <main class="container">
    <h1 style="margin-top:.5rem;">Tableau de bord</h1>

    <div class="tabs">
      <button class="tab-btn active" data-tab="stats">Statistiques</button>
      <button class="tab-btn" data-tab="covoit">Gestion des covoiturages</button>
      <button class="tab-btn" data-tab="employes">Gestion des employés</button>
    </div>

    <!-- Onglet "statistiques" -->
    <section id="tab-stats" class="tab-content active">
      <div class="row" style="justify-content: space-between;">
        <div class="row">
          <label for="granularity" style="font-weight:600;">Granularité :</label>
          <select id="granularity" class="select">
            <option value="jour">Jour</option>
            <option value="mois" selected>Mois</option>
            <option value="annee">Année</option>
          </select>
        </div>
        <div class="kpi">Total crédits : <strong id="totalCredits">…</strong></div>
      </div>

      <div class="grid grid-2">
        <div class="card">
          <h3>Trajets</h3>
          <div class="chart-wrap"><canvas id="chartTrajets"></canvas></div>
        </div>
        <div class="card">
          <h3>Crédits</h3>
          <div class="chart-wrap"><canvas id="chartCredits"></canvas></div>
        </div>
      </div>
    </section>

    <!-- Onglet "gestion des covoiturages" -->
    <section id="tab-covoit" class="tab-content">
      <div class="subtabs">
        <button class="subtab-btn active" data-subtab="avis">Avis</button>
        <button class="subtab-btn" data-subtab="conflits">Conflits</button>
      </div>

      <div id="subtab-avis" class="subtab-content">
        <div id="listeAvis" class="grid"></div>
      </div>

      <div id="subtab-conflits" class="subtab-content hidden">
        <div id="listeConflits" class="grid"></div>
      </div>
    </section>

    <!-- Onglet "gestion des employés" -->
    <section id="tab-employes" class="tab-content">
      <div class="card" style="margin-bottom:1rem">
        <h3>Créer un employé</h3>
        <form id="formCreateEmploye" class="grid grid-2" autocomplete="off">
          <input type="text"     name="nom"       placeholder="Nom" required />
          <input type="text"     name="prenom"    placeholder="Prénom" required />
          <input type="email"    name="email"     placeholder="Email" required />
          <input type="tel"      name="telephone" placeholder="Téléphone" />
          <input type="password" name="password"  placeholder="Mot de passe temporaire" required />
          <div><button class="btn primary" type="submit">Créer l’employé</button></div>
        </form>
        <div id="createEmpMsg"></div>
      </div>

      <div class="card">
        <h3>Employés & rôles</h3>
        <table class="table" id="tableEmployes">
          <thead>
            <tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
  (function(){
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabs = {
      stats:   document.getElementById('tab-stats'),
      covoit:  document.getElementById('tab-covoit'),
      employes:document.getElementById('tab-employes'),
    };
    tabBtns.forEach(b => b.addEventListener('click', () => {
      tabBtns.forEach(x => x.classList.remove('active'));
      Object.values(tabs).forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      tabs[b.dataset.tab].classList.add('active');

      if (b.dataset.tab === 'stats')    loadStats();
      if (b.dataset.tab === 'covoit')   { loadAvis(); loadConflits(); }
      if (b.dataset.tab === 'employes') loadEmployes();
    }));

    const subBtns = document.querySelectorAll('.subtab-btn');
    const subAvis = document.getElementById('subtab-avis');
    const subConflits = document.getElementById('subtab-conflits');
    subBtns.forEach(b => b.addEventListener('click', () => {
      subBtns.forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      if (b.dataset.subtab === 'avis') {
        subAvis.classList.remove('hidden'); subConflits.classList.add('hidden');
        loadAvis();
      } else {
        subConflits.classList.remove('hidden'); subAvis.classList.add('hidden');
        loadConflits();
      }
    }));

    // Statistiques
    const $granularity = document.getElementById('granularity');
    const $totalCredits = document.getElementById('totalCredits');
    let charts = { trajets: null, credits: null };
    let statsCache = null;

    $granularity.addEventListener('change', () => { if (statsCache) renderCharts(statsCache, $granularity.value); });

    async function loadStats() {
      try {
        const res = await fetch('index.php?page=stats_json', { credentials:'include' });
        const data = await res.json();
        statsCache = data;
        $totalCredits.textContent = data?.totalCredits ?? 'N/A';
        renderCharts(data, $granularity.value);
      } catch (e) {
        console.error(e);
        $totalCredits.textContent = 'Erreur';
        destroyCharts();
      }
    }

    function destroyCharts(){
      if (charts.trajets) { charts.trajets.destroy(); charts.trajets = null; }
      if (charts.credits) { charts.credits.destroy(); charts.credits = null; }
    }

    function buildSeries(arr, keyVal='total') {
      if (!Array.isArray(arr)) return { labels: [], values: [] };
      const copy = arr.slice();
      copy.sort((a,b)=>{
        const A=a.periode, B=b.periode;
        return A.localeCompare(B);
      });
      const labels = copy.map(x => x.periode);
      const values = copy.map(x => Number(x[keyVal] ?? 0));
      return { labels, values };
    }

    function renderCharts(data, granularity) {
      destroyCharts();
      const ctxT = document.getElementById('chartTrajets').getContext('2d');
      const ctxC = document.getElementById('chartCredits').getContext('2d');

      const traj = buildSeries(data?.trajets?.[granularity] ?? [], 'total');
      const cred = buildSeries(data?.credits?.[granularity] ?? [], 'credits');

      charts.trajets = new Chart(ctxT, {
        type: 'line',
        data: {
          labels: traj.labels,
          datasets: [{ label: `Trajets par ${granularity}`, data: traj.values, tension: .25, fill: true }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { ticks: { autoSkip: true, maxTicksLimit: 12 } },
            y: { beginAtZero: true, ticks: { precision:0 } }
          },
          plugins: { legend: { display: true } }
        }
      });

      charts.credits = new Chart(ctxC, {
        type: 'bar',
        data: {
          labels: cred.labels,
          datasets: [{ label: `Crédits par ${granularity}`, data: cred.values }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { ticks: { autoSkip: true, maxTicksLimit: 12 } },
            y: { beginAtZero: true, ticks: { precision:0 } }
          },
          plugins: { legend: { display: true } }
        }
      });
    }

    // Avis
    async function loadAvis() {
      try {
        const res = await fetch('index.php?page=lister_avis', { credentials:'include' });
        const data = await res.json();
        const container = document.getElementById('listeAvis');
        container.innerHTML = '';
        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = '<p>Aucun avis à valider.</p>'; return;
        }
        data.forEach(avis => {
          const div = document.createElement('div');
          div.className = 'card';
          div.innerHTML = `
            <p><strong>Utilisateur :</strong> ${avis.utilisateur_nom} (${avis.utilisateur_email})</p>
            <p><strong>Trajet ID :</strong> ${avis.trajet_id}</p>
            <p><strong>Note :</strong> ${avis.note} ★</p>
            <p><strong>Avis :</strong> ${avis.commentaire}</p>
            <div class="row">
              <button class="btn primary small" data-action="valider" data-id="${avis.id}">Valider</button>
              <button class="btn danger small"  data-action="refuser" data-id="${avis.id}">Refuser</button>
            </div>`;
          container.appendChild(div);
        });

        container.addEventListener('click', async (e) => {
          const btn = e.target.closest('button[data-action]');
          if (!btn) return;
          const valide = btn.dataset.action === 'valider';
          try {
            await fetch('index.php?page=modifier_avis', {
              method: 'POST',
              headers: { 'Content-Type':'application/json' },
              credentials: 'include',
              body: JSON.stringify({ id: btn.dataset.id, valide })
            });
            loadAvis();
          } catch (err) { console.error(err); }
        }, { once:true });

      } catch (e) { console.error(e); }
    }

    // Conflits
    async function loadConflits() {
      try {
        const res = await fetch('index.php?page=lister_conflits', { credentials:'include' });
        const data = await res.json();
        const container = document.getElementById('listeConflits');
        container.innerHTML = '';
        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = '<p>Aucun conflit en cours.</p>'; return;
        }
        data.forEach(c => {
          const div = document.createElement('div');
          div.className = 'card';
          div.innerHTML = `
            <p><strong>Trajet ID :</strong> ${c.trajet_id} (État: ${c.etat})</p>
            <p><strong>Départ :</strong> ${c.lieu_depart} → <strong>Arrivée :</strong> ${c.lieu_arrivee}</p>
            <p><strong>Date :</strong> ${c.date}</p>
            <h4>Participants :</h4>
            <ul>
              <li>${c.utilisateur_note.nom} (${c.utilisateur_note.email}) - Note reçue: ${c.note_recue} ★</li>
              <li>${c.utilisateur_noteur.nom} (${c.utilisateur_noteur.email}) - Note donnée: ${c.note_donnee} ★</li>
            </ul>
            <div class="row">
              <button class="btn primary small" data-action="resolu"  data-id="${c.trajet_id}">Résolu</button>
              <button class="btn small"           data-action="nonres" data-id="${c.trajet_id}">Non-résolu</button>
            </div>`;
          container.appendChild(div);
        });

        container.addEventListener('click', async (e) => {
          const btn = e.target.closest('button[data-action]');
          if (!btn) return;
          try {
            if (btn.dataset.action === 'resolu') {
              await fetch('index.php?page=modifier_conflit', {
                method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include',
                body: JSON.stringify({ trajet_id: btn.dataset.id, etat: 'resolu' })
              });
            } else {
              const choix = prompt("Distribuer crédits à :\n1 - Les deux\n2 - Participant noté\n3 - Participant noteur\n4 - Aucun");
              if (!choix) return;
              await fetch('index.php?page=modifier_conflit', {
                method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include',
                body: JSON.stringify({ trajet_id: btn.dataset.id, etat: 'non-resolu', distribution_credits: choix })
              });
            }
            loadConflits();
          } catch (err) { console.error(err); }
        }, { once:true });

      } catch (e) { console.error(e); }
    }

    // Employés
    const $tbody = document.querySelector('#tableEmployes tbody');
    const $formCreate = document.getElementById('formCreateEmploye');
    const $msg = document.getElementById('createEmpMsg');

    async function loadEmployes() {
      try {
        const res = await fetch('index.php?page=admin_list_users&roles=admin,employe,user', { credentials:'include' });
        const data = await res.json();
        $tbody.innerHTML = '';
        (data.users || []).forEach(u => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.prenom} ${u.nom}</td>
            <td>${u.email}</td>
            <td><span class="pill">${u.role}</span></td>
            <td class="row">
              ${u.role !== 'admin'   ? `<button class="btn small" data-action="promote-admin" data-id="${u.id}">Admin</button>`   : ''}
              ${u.role !== 'employe' ? `<button class="btn small" data-action="set-employe"    data-id="${u.id}">Employé</button>`: ''}
              ${u.role !== 'user'    ? `<button class="btn small danger" data-action="set-user" data-id="${u.id}">Utilisateur</button>`: ''}
            </td>`;
          $tbody.appendChild(tr);
        });
      } catch (e) {
        console.error(e);
        $tbody.innerHTML = '<tr><td colspan="5">Erreur de chargement</td></tr>';
      }
    }

    $tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button[data-action]');
      if (!btn) return;
      const map = { 'promote-admin':'admin', 'set-employe':'employe', 'set-user':'user' };
      const role = map[btn.dataset.action];
      if (!role) return;

      try {
        const res = await fetch('index.php?page=admin_update_role', {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          credentials:'include',
          body: JSON.stringify({ id: parseInt(btn.dataset.id,10), role })
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Erreur'); return; }
        loadEmployes();
      } catch (e2) { console.error(e2); }
    });

    $formCreate?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData($formCreate);
      const payload = Object.fromEntries(fd.entries());
      try {
        const res = await fetch('index.php?page=admin_create_employe', {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          credentials:'include',
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        $msg.textContent = data.message || (data.success ? 'Employé créé' : 'Erreur');
        if (data.success) { $formCreate.reset(); loadEmployes(); }
      } catch (e2) {
        console.error(e2);
        $msg.textContent = 'Erreur serveur';
      }
    });

    // Démarrage par défaut
    loadStats();
  })();
  </script>
</body>
</html>
