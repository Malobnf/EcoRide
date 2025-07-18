document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('preferencesForm');
  const checkboxes = form.querySelectorAll('input[type="checkbox"]');
  const messageDiv = document.getElementById('prefMessage');

  // Charger préférences existantes
  fetch('get_user_preferences.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const prefs = data.preferences || [];
        checkboxes.forEach(cb => {
          cb.checked = prefs.includes(cb.value);
        });
      } else {
        messageDiv.textContent = "Erreur de chargement des préférences";
      }
    })
    .catch(() => {
      messageDiv.textContent = "Erreur réseau";
    });

  // Envoyer la maj des préférences au serveur
  form.addEventListener('change', () => {
    const selected = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    fetch('update_preferences.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({preferences: selected})
    })
    .then(response => response.json())
    .then(data => {
      messageDiv.textContent = data.message || '';
    })
    .catch(() => {
      messageDiv.textContent = "Erreur lors de la mise à jour des préférences";
    });
  });
});

let reservationASupprimer = null;

async function chargerMesTrajets() {
  const response = await fetch('mes_trajets.php');
  const data = await response.json();

  if(data.success) {
    const container = document.getElementById('listeTrajets');
    
    container.innerHTML = data.trajets.map(trajet => {
      // Affichage rôle conducteur ou passager
      const role = trajet.role === 'conducteur' ? 'Conducteur' : 'Passager';

      // Bouton annuler uniquement si c’est une réservation (passager)
      const btnAnnuler = trajet.trajet_id 
        ? `<button class="annuler-btn" data-id="${trajet.trajet_id}">Annuler</button>`
        : '';

      return `
        <div class="trajet" ${trajet.trajet_id ? `data-reservation-id="${trajet.trajet_id}"` : ''}>
          <p>${trajet.ville_depart} vers ${trajet.ville_arrivee}, le ${trajet.date_trajet}</p>
          <p>Conducteur : ${trajet.conducteur} | Rôle : ${role} | Prix : ${trajet.prix ?? 'N/A'} crédits</p>
          ${btnAnnuler}
        </div>
      `;
    }).join('');

    // Gestion du clic annuler
    document.querySelectorAll('.annuler-btn').forEach(btn => {
      btn.addEventListener('click', e => {
        reservationASupprimer = e.target.dataset.id;
        document.getElementById('popupConfirm').classList.remove('hidden');
      });
    });
  } else {
    alert("Erreur : " + data.message);
  }
}

document.getElementById('confirmerAnnulation').addEventListener('click', async () => {
  if (!reservationASupprimer) return;

  const res = await fetch('annuler_reservation.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({trajet_id: reservationASupprimer})
  });

  const result = await res.json();
  if (result.success) {
    alert("Réservation annulée.");
    console.log("Suppression du trajet avec ID:", reservationASupprimer);
    console.log(document.querySelector(`.trajet[data-reservation-id="${reservationASupprimer}"]`));
    const trajetDiv = document.querySelector(`.trajet[data-reservation-id="${reservationASupprimer}"]`);
    if (trajetDiv) trajetDiv.remove();
  } else {
    alert("Erreur : " + result.message);
  }

  document.getElementById('popupConfirm').classList.add('hidden');
  reservationASupprimer = null;
});

  document.getElementById('annulerAnnulation').addEventListener('click', () => {
    document.getElementById('popupConfirm').classList.add('hidden');
    reservationASupprimer = null;
});

document.getElementById('mesTrajetsTab').addEventListener('click', () => {
  document.getElementById('mesTrajetsContent').classList.toggle('hidden');
});


chargerMesTrajets();
