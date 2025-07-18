let trajetSelectionne = null;
document.addEventListener('DOMContentLoaded', () => {
  initRechercheCovoitPage();
  initResultatsCovoit();
  });

function initRechercheCovoitPage() {

  const departInput = document.getElementById('departVille');
  const arriveeInput = document.getElementById('arriveeVille');
  const dateInput = document.getElementById('departDate');
  const searchBtn = document.getElementById('searchBtn');
  const resultatsDiv = document.getElementById('resultats');
  const messageDiv = document.getElementById('message');

  if (!searchBtn) return;

  searchBtn.addEventListener('click', async () => {
    const depart = departInput.value.trim();
    const arrivee = arriveeInput.value.trim();
    const date = dateInput.value;


    if (!depart || !arrivee || !date) {
      messageDiv.textContent = "Veuillez remplir tous les champs.";
      resultatsDiv.classList.add('hidden');
      return;
    }

    // Requête AJAX vers PHP
    try {
      const response = await fetch('rechercher_trajets.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({depart, arrivee, date})
      });

      const data = await response.json();
      console.log("Données reçues :", data);

    // Résultats trouvés
      if (data.success && data.trajets.length > 0) {
        resultatsDiv.innerHTML = data.trajets.map((trajet, index) => `
          <div class="trajet-card">
            <p><strong>${trajet.conducteur}</strong> - ${trajet.date_trajet} à ${trajet.heure_depart}</p>
            <p>De <strong>${trajet.ville_depart}</strong> à <strong>${trajet.ville_arrivee}</strong></p>
            <p>Prix : ${trajet.prix} crédits - Places disponibles : ${trajet.places_disponibles}</p>
            <button class="detail-btn" data-index="${index}">Détails</button>
          </div>
        `).join('');
        resultatsDiv.classList.remove('hidden');

    // Lier les listeners au bouton "détails"
        document.querySelectorAll('.detail-btn').forEach(btn => {
          btn.addEventListener('click', (e) => {
            const index = e.target.dataset.index;
            const trajet = data.trajets[index];
            trajetSelectionne = trajet;

            document.getElementById('modalNom').textContent = trajet.conducteur;
            document.getElementById('modalDate').textContent = trajet.date_trajet;
            document.getElementById('modalHeure').textContent = trajet.heure_depart;
            document.getElementById('modalDepart').textContent = trajet.ville_depart;
            document.getElementById('modalArrivee').textContent = trajet.ville_arrivee;
            document.getElementById('modalPrix').textContent = trajet.prix;
            document.getElementById('modalPlaces').textContent = trajet.places_disponibles;

            document.getElementById('modalPhoto').src = 'default-user.png';
            document.getElementById('modalRating').textContent = '⭐⭐⭐☆';

            document.getElementById('trajetModal').classList.remove('hidden');

            const reserverBtn = document.getElementById('reserverBtn');
            reserverBtn.onclick = async () => {
              if (!trajetSelectionne) return;

              try {
              const response = await fetch('reserver.php', {
                method: 'POST',
                headers: {'Content-Type' : 'application/json'},
                credentials: 'include',
                body: JSON.stringify({id_trajet: trajetSelectionne.id})
            });

              const result = await response.json();
              if (result.success) {
                alert("Réservation confirmée !");
                document.getElementById('trajetModal').classList.add('hidden');
              } else {
                alert("Erreur : " + result.message);
              }
            } catch (err) {
              console.error("Erreur lors de la réservation", err);
            }
          };
        });
      });
          
      messageDiv.textContent = "";
      } else {
        resultatsDiv.classList.add('hidden');
        messageDiv.textContent = "Aucun trajet disponible.";
      }      

    } catch (error) {
      console.error("Erreur réseau", error);
      messageDiv.textContent = "Erreur lors de la recherche.";
      resultatsDiv.classList.add('hidden');
    }
  });

  document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('trajetModal').classList.add('hidden');
  })
}

// Covoiturages disponibles

function initResultatsCovoit() {
  
  const params = new URLSearchParams(window.location.search);
  const destination = params.get('destination');
  const depart = params.get('depart');
  const titre = document.getElementById('input-recherche');

  // Maj titre
  if (titre) {
    if (depart && destination) {
      titre.textContent = `Trajet de ${depart} à ${destination}`
    } else if (destination) {
      titre.textContent = `Trajet vers ${destination}`;
    } else if (depart) {
      titre.textContent = `Départ de ${depart}`;
    } else {
      titre.textContent = `Rechercher un covoiturage`;
    }
  }
}