let chartTrajets, chartCredits;

async function chargerStats(period) {
  try {
    const res = await fetch('stats.php');
    const data = await res.json();

    const trajetsData = data.trajets[periode];
    const creditsData = data.credits[periode];

    const labelsTrajets = trajetsData.map(e => e.periode);
    const valuesTrajets = trajetsData.map(e => e.credits);
  
    const labelsCredits = creditsData.map(e => e.periode);
    const valuesCredits = creditsData.map(e => e.credits);
    
    const ctxTrajets = document.getElementById('chartTrajets').getContext('2d');
    if (chartTrajets) chartTrajets.destroy();
    chartTrajets = new Chart(ctxTrajets, {
      type: 'line',
      data: {
        labels: labelsTrajets,
        datasets: [{
          label: "Nombre de covoiturages",
          data: valuesTrajets,
          borderColor: 'blue',
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { display: true },
          y: { beginAtZero: true },
        }
      }
    });
    
    const ctxCredits = document.getElementById('chartCredits').getContext('2d');
    if (chartCredits) chartCredits.destroy();
    chartCredits = new Chart(ctxCredits, {
      type: 'line',
      data: {
        labels: labelsCredits,
        datasets: [{
          label: "Crédits accumulés par EcoRide",
          data: valuesCredits,
          borderColor: 'green',
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { display: true },
          y: { beginAtZero: true },
        }
      }
    });

    document.getElementById('totalCredits').textContent = data.totalCredits + " crédits";

  } catch (err) {
    console.error("Erreur lors du chargement des statistiques :", err);
  }
}

document.getElementById('periodeSelect').addEventListener('change', e => {
  chargerStats(e.target.value);
});

chargerStats('jour');