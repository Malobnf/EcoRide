// alerteTrajet.js

function reserverTrajet(trajetId, userId) {
  fetch('index.php?page=alerte_trajet', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({ action: 'reserver', trajet_id: trajetId, utilisateur_id: userId })
  })
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById('alertContainer');
    if (data.success) {
      alert(data.message);
      container.innerHTML = ''; // Clear alert if any
      // Actualise l'affichage des réservations, etc.
    } else if (data.full) {
      container.innerHTML = `
        <p>Plus de places disponibles.</p>
        <button id="alerteBtn">Prévenir quand une place se libère</button>
      `;
      document.getElementById('alerteBtn').onclick = () => {
        envoyerAlertePlaceLiberee(trajetId, userId);
      };
    } else {
      alert(data.message);
    }
  })
  .catch(err => console.error(err));
}

function envoyerAlertePlaceLiberee(trajetId, userId) {
  fetch('index.php?page=alerte_trajet', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({ action: 'demander_alerte', trajet_id: trajetId, utilisateur_id: userId })
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.success) {
      document.getElementById('alertContainer').innerHTML = '';
    }
  })
  .catch(err => console.error(err));
}
