<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
<script src="../js/script.js"></script>
<title>EcoRide</title>
</head>

<body>
<header>
<div class="deroulant">☰</div>
<div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
<div class="profile-icone">
  <a href="index.php?page=profil" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
  </a>
</div>

    <nav class="side-menu" id="sideMenu">
      <a class="current-page">Accueil</a>
      <a href="index.php?page=covoit">Recherche</a>
      <a href="index.php?page=creer-trajet_html">Proposer un trajet</a>
      <a href="index.php?page=profil">Profil</a>
      <a href="index.php?page=contact">Contact</a>
    </nav>
  </header>

  <div class="container">
    <p>On va où ?</p>
</br>
    <a class="searchBtn search-btn-trans" href="index.php?page=covoit">
      <span>Rechercher un trajet</span>
    </a>
    <!-- <button id="searchBtn" class="searchBtn search-btn-trans" href=>Rechercher un trajet </button> <i class="fas fa-arrow-right"></i> -->
  </div>

  <div class="about_us">
    <h3>EcoRide</h3>
  </br>
    <p class="presentation">EcoRide est LE nouveau site de covoiturage éco-friendly. Chez EcoRide, nous prenons soin de pouvoir vous proposer des alternatives soucieuses de l'environnement et permettre ainsi à chacun de pouvoir agir à son échelle pour soutenir la nature. Choisissez votre mode de transport, vos trajets et vos horaires et restez informés de votre empreinte carbone et de votre impact direct pour la sauvegarde de notre planète. Tout ça en réalisant en plus des économies !</br> N'attendez plus et réservez votre trajet dès maintenant ! </p>
  </div>

    <h3>L'équipe</h3>
</br>
</br>
</br>
</br>
</br>


    <div class="carousel">
      <img src="../Images/photos-equipe/img02.jpg" alt="Photo cheffe de projet" class="carousel-image" data-description="Jane Doe, cheffe de projet">
      <img src="../Images/photos-equipe/img03.jpg" alt="Photo trésorière" class="carousel-image" data-description="Jane Doe, trésorière">
      <img src="../Images/photos-equipe/img01.jpg" alt="Photo directeur" class="carousel-image active" data-description="John Doe, directeur">
      <img src="../Images/photos-equipe/img04.jpg" alt="Photo développeur" class="carousel-image" data-description="John Doe, développeur">
      <img src="../Images/photos-equipe/img05.jpg" alt="Photo développeuse" class="carousel-image" data-description="Jane Doe, développeuse">
    </div>
  
  <div class="carousel-description" id="carouselDescription">
    Description de l'image
  </div>

</br>
<h3>Statistiques</h3>
<br/>

<div class="statistics">
  <div id="trajets">
    <h4>Nombre de covoiturages depuis le <span id="sinceDate">01/01/2025</span></h4>
    <span id="item1">…</span>
  </div>

  <div id="co">
    <h4>CO₂ économisé en moyenne</h4>
    <span id="item3">…</span>
  </div>

  <div id="argent">
    <h4>Argent économisé par utilisateur en moyenne</h4>
    <span id="item5">…</span>
  </div>
</div>


  <footer>
    <div>
      <a rel="mentions-legales" href="../mentions-legales.pdf" target="_blank">Mentions légales</a>
    </div>
    <div>
      <a rel="contact" href="contact.php?page=contact">Contact</a>
    </div>
  </footer>

<script>
(async function(){
  // Helper formatage
  const fmtInt = (n) => new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.round(n));
  const fmtEur = (n) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n);
  const fmtCO2  = (kg) => {
    if (kg >= 1000) return (kg/1000).toFixed(1).replace('.', ',') + ' t';
    return fmtInt(kg) + ' kg';
  };

  // Animation
  function animateCount(el, target, duration=800) {
    const start = 0, delta = target - start;
    const t0 = performance.now();
    function tick(now){
      const p = Math.min(1, (now - t0)/duration);
      el.textContent = fmtInt(start + delta * p);
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  try {
    const res = await fetch('index.php?page=home_stats', { credentials:'include' });
    const data = await res.json();
    if (!data || data.success === false) throw new Error(data?.message || 'Erreur de stats');

    // Date "depuis"
    const since = data.since || '2025-01-01';
    const [y,m,d] = since.split('-');
    document.getElementById('sinceDate').textContent = `${d}/${m}/${y}`;

    // Covoiturages
    const item1 = document.getElementById('item1');
    animateCount(item1, data.trips ?? 0);

    // CO2 économisé
    const item3 = document.getElementById('item3');
    item3.textContent = fmtCO2(data.co2SavedKg ?? 0);

    // Argent économisé par utilisateur (moyenne)
    const item5 = document.getElementById('item5');
    item5.textContent = fmtEur(data?.money?.perUserEur ?? 0);

  } catch (e) {
    console.error(e);
    document.getElementById('item1').textContent = '—';
    document.getElementById('item3').textContent = '—';
    document.getElementById('item5').textContent = '—';
  }
})();
</script>


</body>
</html>