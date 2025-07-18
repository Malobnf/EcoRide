document.addEventListener('DOMContentLoaded', () => {
  
  //Gérer ouverture et fermeture du popup trajets
  const openBtn = document.getElementById('openTrajetsModal');
  const closeBtn = document.getElementById('closeTrajetsModal');
  const modalOverlay = document.getElementById('trajetsModalOverlay');

  openBtn.addEventListener('click', () => {
    modalOverlay.classList.remove('hidden');
    chargerMesTrajets();
  });

  closeBtn.addEventListener('click', () => {
    console.log("fermeture");
    modalOverlay.classList.add('hidden');
  });

  // Fermer si on clique en dehors du modal
  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
      modalOverlay.classList.add('hidden');
    }
  })  

  // Navigation entre onglets dans le popup
  document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
      
      // Retirer l'état actif des onglets
      document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.modal-tab-content').forEach(tab => tab.style.display = 'none');

      //Activer l'onglet sélectionné
      button.classList.add('active');
      const tabId = button.dataset.tab;
      document.getElementById(tabId).style.display = 'block';
    });
  });

  // Afficher le premier onglet par défaut
  document.getElementById('futurs').style.display= 'block';
  document.getElementById('passes').style.display= 'none';

  // Préférences utilisateur
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

let reservationASupprimer = null;

async function chargerMesTrajets() {
  const response = await fetch('mes_trajets.php');
  const data = await response.json();

  const futursContainers = document.getElementById('listeTrajetsFuturs');
  const passesContainers = document.getElementById('listeTrajetsPasses');
  futursContainers.innerHTML = '';
  passesContainers.innerHTML = '';

  if(data.success) {
    const now = new Date();
    const trajets = data.trajets;

    if (trajets.length === 0) {    
      futursContainers.innerHTML = `<p>Vous n'avez aucun trajet de prévu.</p>`;
      passesContainers.innerHTML = `<p>Aucun trajet passé.</p>`;
      return;
    }

    trajets.forEach(trajet => {
      const dateTrajet = new Date(trajet.date_trajet);
      const role = trajet.role === 'conducteur' ? 'Conducteur' : 'Passager';
      const contenu = `
        <div class="trajet" ${trajet.reservation_id ? `data-reservation-id="${trajet.reservation_id}"` : ''}>
          <p>${trajet.ville_depart} → ${trajet.ville_arrivee} (${trajet.date_trajet})</p>
          <p>Conducteur : ${trajet.conducteur} | Rôle : ${role} | Prix : ${trajet.prix ?? 'N/A'} crédits</p>
          ${trajet.role === 'passager' && trajet.reservation_id ? 
          `<button class="annuler-btn" data-id="${trajet.reservation_id}">Annuler</button>` : ''}
        </div>
      `;

      if (dateTrajet > now) {
        futursContainers.innerHTML += contenu;
      } else {
        passesContainers.innerHTML += contenu;
      }        
    });

    // Bouton annuler
    document.querySelectorAll('.annuler-btn').forEach(btn => {
      btn.addEventListener('click', e => {
        reservationASupprimer = e.target.dataset.id;
        document.getElementById('popupConfirm').classList.remove('hidden');
      });
    });

  } else {
    futursContainers.innerHTML = "<p>Erreur : " + data.message + "</p>";
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
    const trajet = document.querySelector(`.trajet[data-reservation-id="${reservationASupprimer}"]`);
    if (trajet) trajet.remove();
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
});