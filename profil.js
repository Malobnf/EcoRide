document.addEventListener('DOMContentLoaded', () => {
  initModInfo();
  
  //Gérer ouverture et fermeture du popup trajets
  const openBtn = document.getElementById('openTrajetsModal');
  const closeBtn = document.getElementById('closeTrajetsModal');
  const modalOverlay = document.getElementById('trajetsModalOverlay');

  if (openBtn && closeBtn && modalOverlay) {
    openBtn.addEventListener('click', () => {
      modalOverlay.classList.remove('hidden');
      chargerMesTrajets();
    });

    closeBtn.addEventListener('click', () => {
      console.log("fermeture");
      modalOverlay.classList.add('hidden');
    });

    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        modalOverlay.classList.add('hidden');
      }
    });
  } else {
    console.warn("Un ou plusieurs éléments modaux sont absents.");
  }  

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


  // Onglet véhicules
  const vehiculeBtn = document.getElementById("vehiculeBtn");
  const vehiculeModal = document.getElementById("vehiculeModalOverlay");
  const closeVehiculeModal = document.getElementById("closeVehiculeModal");
  const listeVehicules = document.getElementById("listeVehicules");
  const formAjoutVehicule = document.getElementById("formAjoutVehicule");
  const ajouterVehiculeBtn = document.getElementById("ajouterVehiculeBtn");
  const formModifVehicule = document.getElementById("formModifVehicule");

  if (vehiculeBtn) {
    vehiculeBtn.addEventListener('click', () => {
    vehiculeModal.classList.remove('hidden');
    chargerVehicules();
  });
    } else {
      console.warn("vehiculeBtn non trouvé");
    }

  if (closeVehiculeModal) {
    closeVehiculeModal.addEventListener('click', () => {
      vehiculeModal.classList.add('hidden');
  });
    } else {
      console.warn("closeVehiculeModal non trouvé");
    }

  if (ajouterVehiculeBtn) {
    ajouterVehiculeBtn.addEventListener('click', () => {
      formAjoutVehicule.classList.toggle('hidden');
  });
    } else {
      console.warn("ajouterVehiculeBtn non trouvé");
    }

  if (formAjoutVehicule) {
    formAjoutVehicule.addEventListener('submit', async (e) => {
      e.preventDefault();
      const data = new FormData(formAjoutVehicule);
      const res = await fetch('api/ajouter_vehicule.php', {
        method: 'POST',
        body: data
      });

      if (res.ok) {
        formAjoutVehicule.reset();
        formAjoutVehicule.classList.add('hidden');
        chargerVehicules();
      }
    });
  } else {
    console.warn("formAjoutVehicule non trouvé");
  }

  if (formModifVehicule) {
    formModifVehicule.addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(formModifVehicule);

      try {
        const res = await fetch('api/modifier_vehicule.php', {
          method: 'POST',
          body: formData
        });


        const text = await res.text();
        console.log("Réponse brute reçue :", text);

        const result = JSON.parse(text);

        if (result.success) {
          alert("Modifications enregistrées.");
          console.log("Masquage du formulaire de modification...");
          formModifVehicule.classList.add("hidden");
          formModifVehicule.reset();
          chargerVehicules(); // Rafraîchir la liste des véhicules
        } else {
          alert(result.message || "Erreur lors de la modification");
        }

      } catch (error) {
        console.error("Erreur attrapée lors du fetch:", error);
        alert("Erreur réseau");      
      }
    });
  }

  async function chargerVehicules() {
    try {
      const res = await fetch ('api/mes_vehicules.php');
      const vehicules = await res.json();
      listeVehicules.innerHTML = "";

      vehicules.forEach(v => {
        const div = document.createElement("div");
        div.innerHTML = `
          <p><strong>${v.marque} ${v.modele}</strong> - ${v.plaque} (${v.couleur})</p>
          <button class="modifierVehiculeBtn" data-id="${v.id}">Modifier</button>
          <button class="supprimer-vehicule-btn" data-id="${v.id}">Supprimer</button>
        `;
        listeVehicules.appendChild(div);
      });

    // Supprimer véhicule
    document.querySelectorAll(".supprimer-vehicule-btn").forEach(btn => {
      btn.addEventListener('click', async () => {
        if(confirm("Supprimer ce véhicule ?")) {
          await fetch('api/supprimer_vehicule.php?id=' + btn.dataset.id);
          chargerVehicules();
        }
      });
    });

    // Modifier véhicule
    document.querySelectorAll(".modifierVehiculeBtn").forEach(btn => {
      btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const res = await fetch('api/get_vehicule.php?id=' + id);
      const data = await res.json();

      if (data.success && data.vehicule) {
        if (formModifVehicule) {
          formModifVehicule.querySelector('[name="id"]').value = data.vehicule.id;
          formModifVehicule.querySelector('[name="marque"]').value = data.vehicule.marque;
          formModifVehicule.querySelector('[name="modele"]').value = data.vehicule.modele;
          formModifVehicule.querySelector('[name="plaque"]').value = data.vehicule.plaque;
          formModifVehicule.querySelector('[name="couleur"]').value = data.vehicule.couleur;
          formModifVehicule.classList.remove('hidden');
          formModifVehicule.scrollIntoView({behavior: 'smooth'});
        }
    } else {
      alert("Erreur : " + (data.message || "Impossible de charger le véhicule."));
    }
  });
});

  } catch (error) {
    console.error(error);
    listeVehicules.innerHTML = '<p>Impossible de charger les véhicules.</p>';
  }

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

// Supprimer réservation
let reservationASupprimer = null;

document.getElementById('confirmerAnnulation').addEventListener('click', async () => {
  if (!reservationASupprimer) return;

  const res = await fetch('annuler_reservation.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({trajet_id: reservationASupprimer})
  });

  const result = await res.json();
  if (result.success) {
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

  if (userRole === 'admin') {
    const adminBtn = document.getElementById('adminTabBtn');
    if (adminBtn) {
      adminBtn.style.display = 'block';
      adminBtn.addEventListener('click', () => {
        window.location.href = 'creer_employe.php';
      });
    }
  }
};

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
      console.log(trajet);
      const dateTrajet = new Date(trajet.date_trajet);
      const role = trajet.role === 'conducteur' ? 'Conducteur' : 'Passager';
      
      if (trajet.etat === 'en cours') {
        actionBtn = `<button class="terminer-btn" data-id="${trajet.id}">Terminer</button>`;
      } else {
        actionBtn = `<span>Trajet terminé</span>`;
      }

      console.log(`ID: ${trajet.id}, état: ${trajet.etat}, rôle: ${trajet.role}`);

      const contenu = `
        <div class="trajet" 
          ${trajet.reservation_id ? `data-reservation-id="${trajet.reservation_id}"` : ''}
          ${trajet.id ? `data-trajet-id="${trajet.id}"` : ''}>
          
          <p>${trajet.ville_depart} → ${trajet.ville_arrivee} (${trajet.date_trajet})</p>
          <p>Conducteur : ${trajet.conducteur} | Rôle : ${role} | Prix : ${trajet.prix ?? 'N/A'} crédits</p>
          
          ${trajet.role === 'passager' && trajet.reservation_id ? 
            `<button class="annuler-btn" data-id="${trajet.reservation_id}">Annuler</button>` : ''
          }

          ${trajet.role === 'conducteur' && trajet.etat === 'à venir' ?
            `<button class="demarrer-btn" data-id="${trajet.id}">Démarrer</button>` : ''
          }

          ${trajet.role === 'conducteur' && trajet.etat === 'en cours' ?
            `<button class="terminer-btn" data-id="${trajet.id}" style="background:red;color:white">Terminer</button>` : ''
          }

          ${trajet.etat === 'terminé' ?
            `<span>Trajet terminé</span>` : ''
          }
        </div>
      `;


      if (dateTrajet > now) {
        futursContainers.innerHTML += contenu;
      } else {
        passesContainers.innerHTML += contenu;
      }        
    });

    // Bouton démarrer covoit
    document.querySelectorAll('.demarrer-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const trajetId = e.target.dataset.id;
        const res = await fetch('changer_etat_trajet.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({id: trajetId, etat: 'en cours'})
        });
        const data = await res.json();
        if (data.success) {
          // Actualisation de la liste
          chargerMesTrajets();
        } else {
          alert("Erreur : " + data.message)
        }
      });
    });

    // Bouton terminer covoit
    document.querySelectorAll('.terminer-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const trajetId = e.target.dataset.id;
        const res = await fetch('changer_etat_trajet.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({id: trajetId, etat: 'terminé'})
        });
        const data = await res.json();
        if (data.success) {
          // Actualisation de la liste
          chargerMesTrajets();
        } else {
          alert("Erreur : " + data.message)
        }
      });
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


// Sauvegarde AJAX
const editForm = document.getElementById('editProfileForm');
if(editForm) {
  editForm.addEventListener('submit', async (e) => {
    e.preventDefault();
      const formData = new FormData(editForm);
      
      try {
        const response = await fetch('modifier_profil.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        
        if (result.success) {
          alert("Profil mis à jour !");
          window.location.reload();
        } else {
          alert(result.message || "Erreur lors de la mise à jour.");
        }
      } catch (error) {
        console.error(error);
        alert("Erreur réseau.");
      }
    })
  }
});

function initModInfo() {
  const profilBtn = document.getElementById('profilBtn');
  const editPopup = document.getElementById('editProfilePopup');
  const closeProfilBtn = document.getElementById('closeEditProfile');

  if (!profilBtn || !editPopup || !closeProfilBtn) {
    console.warn("Un ou plusieurs éléments sont absents.")
    return;
  }
  // Affichage du popup
  profilBtn.addEventListener('click', () => {
    editPopup.classList.remove('hidden');
  });

  // Fermeture du popup
  closeProfilBtn.addEventListener('click', () => {
    editPopup.classList.add('hidden');
  });

  // Activer les champs de modification
  document.querySelectorAll('.edit-icon').forEach(icon => {
    icon.addEventListener('click', () => {
      const target = icon.dataset.target;
      const span = document.getElementById(`${target}Text`);
      const input = document.getElementById(`${target}Input`);

      // Remplir le champ "input" avec le texte actuel
      input.value = span.textContent.trim();

      span.classList.add('hidden');
      input.classList.remove('hidden');
      input.focus();
        });
      });
    }