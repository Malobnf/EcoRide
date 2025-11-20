document.addEventListener('DOMContentLoaded', () => {
  initMenuToggle();
  initMesTrajetsOnglets();
  chargerMesTrajets();
  initAnnulationTrajet();
});

function initMesTrajetsOnglets() {
  const buttons = document.querySelectorAll('.tab-button');
  const tabFuturs = document.getElementById('futurs');
  const tabPasses = document.getElementById('passes');

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');

      const tabId = button.dataset.tab;
      if (tabId === 'futurs') {
        tabFuturs.style.display = 'block';
        tabPasses.style.display = 'none';
      } else {
        tabFuturs.style.display = 'none';
        tabPasses.style.display = 'block';
      }
    });
  });
}

async function chargerMesTrajets() {
  const futursContainers = document.getElementById('listeTrajetsFuturs');
  const passesContainers = document.getElementById('listeTrajetsPasses');

  futursContainers.innerHTML = '';
  passesContainers.innerHTML = '';

  try {
    const response = await fetch('index.php?page=mes_trajets');
    const data = await response.json();

    if (!data.success) {
      futursContainers.innerHTML = "<p>Erreur : " + (data.message || "Impossible de charger vos trajets.") + "</p>";
      return;
    }

    const now = new Date();
    const trajets = data.trajets;

    if (!trajets || trajets.length === 0) {
      futursContainers.innerHTML = `<p>Vous n'avez aucun trajet de prévu.</p>`;
      passesContainers.innerHTML = `<p>Aucun trajet passé.</p>`;
      return;
    }

    trajets.forEach(trajet => {
      const dateTrajet = new Date(trajet.date_trajet);
      const role = trajet.role === 'conducteur' ? 'Conducteur' : 'Passager';

      let actions = '';

      if (trajet.role === 'passager' && trajet.reservation_id) {
        actions += `<button class="annuler-btn" data-id="${trajet.reservation_id}">Annuler</button>`;
      }

      if (trajet.role === 'conducteur' && trajet.etat === 'à venir') {
        actions += `<button class="demarrer-btn" data-id="${trajet.id}">Démarrer</button>`;
      }

      if (trajet.role === 'conducteur' && trajet.etat === 'en cours') {
        actions += `<button class="terminer-btn" data-id="${trajet.id}" style="background:red;color:white">Terminer</button>`;
      }

      if (trajet.etat === 'terminé') {
        actions += `<span>Trajet terminé</span>`;
      }

      const contenu = `
        <div class="trajet" ${trajet.reservation_id ? `data-reservation-id="${trajet.reservation_id}"` : ''}>
          <p><strong>${trajet.ville_depart} → ${trajet.ville_arrivee}</strong> (${trajet.date_trajet})</p>
          <p>Conducteur : ${trajet.conducteur} | Rôle : ${role} | Prix : ${trajet.prix ?? 'N/A'} crédits</p>
          <div class="trajet-actions">${actions}</div>
        </div>
      `;

      if (dateTrajet > now) {
        futursContainers.innerHTML += contenu;
      } else {
        passesContainers.innerHTML += contenu;
      }
    });

    document.querySelectorAll('.demarrer-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const trajetId = e.target.dataset.id;
        const res = await fetch('index.php?page=changer_etat_trajet', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({id: trajetId, etat: 'en cours'})
        });
        const data = await res.json();
        if (data.success) {
          chargerMesTrajets();
        } else {
          alert("Erreur : " + data.message);
        }
      });
    });

    document.querySelectorAll('.terminer-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const trajetId = e.target.dataset.id;
        const res = await fetch('index.php?page=changer_etat_trajet', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({id: trajetId, etat: 'terminé'})
        });
        const data = await res.json();
        if (data.success) {
          chargerMesTrajets();
        } else {
          alert("Erreur : " + data.message);
        }
      });
    });

    document.querySelectorAll('.annuler-btn').forEach(btn => {
      btn.addEventListener('click', e => {
        window.reservationASupprimer = e.target.dataset.id;
        document.getElementById('popupConfirm').classList.remove('hidden');
      });
    });

  } catch (error) {
    console.error(error);
    futursContainers.innerHTML = '<p>Erreur réseau lors du chargement des trajets.</p>';
  }
}

function initAnnulationTrajet() {
  const confirmer = document.getElementById('confirmerAnnulation');
  const annuler   = document.getElementById('annulerAnnulation');
  const popup     = document.getElementById('popupConfirm');

  if (!confirmer || !annuler || !popup) return;

  confirmer.addEventListener('click', async () => {
    if (!window.reservationASupprimer) return;

    const res = await fetch('index.php?page=annuler_reservation', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({trajet_id: window.reservationASupprimer})
    });
    const result = await res.json();
    if (result.success) {
      const trajet = document.querySelector(`.trajet[data-reservation-id="${window.reservationASupprimer}"]`);
      if (trajet) trajet.remove();
    } else {
      alert("Erreur : " + result.message);
    }

    popup.classList.add('hidden');
    window.reservationASupprimer = null;
  });

  annuler.addEventListener('click', () => {
    popup.classList.add('hidden');
    window.reservationASupprimer = null;
  });
}
