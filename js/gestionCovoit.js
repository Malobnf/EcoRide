document.addEventListener('DOMContentLoaded', () => {
  const btnGestion = document.getElementById('gestionCovoitBtn');
  const modalOverlay = document.getElementById('gestionCovoitModalOverlay');
  const closeModalBtn = document.getElementById('closeGestionCovoitModal');
  const tabButtons = modalOverlay.querySelectorAll('.tab-button');
  const tabContents = modalOverlay.querySelectorAll('.modal-tab-content');

  // Ouvrir popup
  if(btnGestion) {
    btnGestion.addEventListener('click', () => {
      modalOverlay.classList.remove('hidden');
      loadAvis();
    });
  }

  // Fermer popup
  closeModalBtn.addEventListener('click', () => {
    modalOverlay.classList.add('hidden');
  });

  // Changer d'onglet
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabContents.forEach(tc => tc.classList.add('hidden'));

      button.classList.add('active');
      document.getElementById(button.dataset.tab).classList.remove('hidden');

      if(button.dataset.tab === 'avis') {
        loadAvis();
      } else if(button.dataset.tab === 'conflits') {
        loadConflits();
      }
    });
  });

  // Charger les avis depuis API
  function loadAvis() {
    fetch('api/lister_avis.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('listeAvis');
        if(data.length === 0) {
          container.innerHTML = '<p>Aucun avis à valider.</p>';
          return;
        }
        container.innerHTML = '';
        data.forEach(avis => {
          const div = document.createElement('div');
          div.classList.add('avis-item');
          div.innerHTML = `
            <p><strong>Utilisateur :</strong> ${avis.utilisateur_nom} (${avis.utilisateur_email})</p>
            <p><strong>Trajet ID :</strong> ${avis.trajet_id}</p>
            <p><strong>Note :</strong> ${avis.note} ★</p>
            <p><strong>Avis :</strong> ${avis.commentaire}</p>
            <button class="valider-btn" data-id="${avis.id}">Valider</button>
            <button class="refuser-btn" data-id="${avis.id}">Refuser</button>
          `;
          container.appendChild(div);
        });

        // Gestion des clics valider/refuser
        container.querySelectorAll('.valider-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            modifierAvis(btn.dataset.id, true);
          });
        });
        container.querySelectorAll('.refuser-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            modifierAvis(btn.dataset.id, false);
          });
        });
      })
      .catch(err => {
        console.error(err);
      });
  }

  // Modifier avis (valider/refuser)
  function modifierAvis(id, valide) {
    fetch('api/modifier_avis.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({id, valide})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadAvis();
    })
    .catch(console.error);
  }

  // Charger les conflits
  function loadConflits() {
    fetch('api/lister_conflits.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('listeConflits');
        if(data.length === 0) {
          container.innerHTML = '<p>Aucun conflit en cours.</p>';
          return;
        }
        container.innerHTML = '';
        data.forEach(conflict => {
          const div = document.createElement('div');
          div.classList.add('conflit-item');
          div.innerHTML = `
            <p><strong>Trajet ID :</strong> ${conflict.trajet_id} (État: ${conflict.etat})</p>
            <p><strong>Départ :</strong> ${conflict.lieu_depart}</p>
            <p><strong>Arrivée :</strong> ${conflict.lieu_arrivee}</p>
            <p><strong>Date :</strong> ${conflict.date}</p>

            <h4>Participants :</h4>
            <ul>
              <li>${conflict.utilisateur_note.nom} (${conflict.utilisateur_note.email}) - Note reçue: ${conflict.note_recue} ★</li>
              <li>${conflict.utilisateur_noteur.nom} (${conflict.utilisateur_noteur.email}) - Note donnée: ${conflict.note_donnee} ★</li>
            </ul>

            <h4>Avis :</h4>
            <p>${conflict.commentaire}</p>

            <button class="resolu-btn" data-id="${conflict.trajet_id}">Résolu</button>
            <button class="non-resolu-btn" data-id="${conflict.trajet_id}">Non-résolu</button>
          `;
          container.appendChild(div);
        });

        container.querySelectorAll('.resolu-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            marquerConflit(btn.dataset.id, 'resolu');
          });
        });
        container.querySelectorAll('.non-resolu-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            marquerConflitNonResolu(btn.dataset.id);
          });
        });
      })
      .catch(console.error);
  }

  // Marquer conflit comme résolu
  function marquerConflit(id, etat) {
    fetch('api/modifier_conflit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({trajet_id: id, etat})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadConflits();
    })
    .catch(console.error);
  }

  // Gestion du non-résolu
  function marquerConflitNonResolu(trajet_id) {
    // Ouvrir un prompt pour choisir à qui distribuer les crédits
    const choix = prompt("Distribuer crédits à :\n1 - Les deux\n2 - Participant noté\n3 - Participant noteur\n4 - Aucun");
    if(!choix) return;

    fetch('api/modifier_conflit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({trajet_id, etat: 'non-resolu', distribution_credits: choix})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadConflits();
    })
    .catch(console.error);
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const btnGestion = document.getElementById('gestionCovoitBtn');
  const modalOverlay = document.getElementById('gestionCovoitModalOverlay');
  const closeModalBtn = document.getElementById('closeGestionCovoitModal');
  const tabButtons = modalOverlay.querySelectorAll('.tab-button');
  const tabContents = modalOverlay.querySelectorAll('.modal-tab-content');

  // Ouvrir popup
  if(btnGestion) {
    btnGestion.addEventListener('click', () => {
      modalOverlay.classList.remove('hidden');
      loadAvis();
    });
  }

  // Fermer popup
  closeModalBtn.addEventListener('click', () => {
    modalOverlay.classList.add('hidden');
  });

  // Changer d'onglet
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabContents.forEach(tc => tc.classList.add('hidden'));

      button.classList.add('active');
      document.getElementById(button.dataset.tab).classList.remove('hidden');

      if(button.dataset.tab === 'avis') {
        loadAvis();
      } else if(button.dataset.tab === 'conflits') {
        loadConflits();
      }
    });
  });

  // Charger les avis depuis API
  function loadAvis() {
    fetch('api/lister_avis.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('listeAvis');
        if(data.length === 0) {
          container.innerHTML = '<p>Aucun avis à valider.</p>';
          return;
        }
        container.innerHTML = '';
        data.forEach(avis => {
          const div = document.createElement('div');
          div.classList.add('avis-item');
          div.innerHTML = `
            <p><strong>Utilisateur :</strong> ${avis.utilisateur_nom} (${avis.utilisateur_email})</p>
            <p><strong>Trajet ID :</strong> ${avis.trajet_id}</p>
            <p><strong>Note :</strong> ${avis.note} ★</p>
            <p><strong>Avis :</strong> ${avis.commentaire}</p>
            <button class="valider-btn" data-id="${avis.id}">Valider</button>
            <button class="refuser-btn" data-id="${avis.id}">Refuser</button>
          `;
          container.appendChild(div);
        });

        // Gestion des clics valider/refuser
        container.querySelectorAll('.valider-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            modifierAvis(btn.dataset.id, true);
          });
        });
        container.querySelectorAll('.refuser-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            modifierAvis(btn.dataset.id, false);
          });
        });
      })
      .catch(err => {
        console.error(err);
      });
  }

  // Modifier avis (valider/refuser)
  function modifierAvis(id, valide) {
    fetch('api/modifier_avis.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({id, valide})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadAvis();
    })
    .catch(console.error);
  }

  // Charger les conflits
  function loadConflits() {
    fetch('api/lister_conflits.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('listeConflits');
        if(data.length === 0) {
          container.innerHTML = '<p>Aucun conflit en cours.</p>';
          return;
        }
        container.innerHTML = '';
        data.forEach(conflict => {
          const div = document.createElement('div');
          div.classList.add('conflit-item');
          div.innerHTML = `
            <p><strong>Trajet ID :</strong> ${conflict.trajet_id} (État: ${conflict.etat})</p>
            <p><strong>Départ :</strong> ${conflict.lieu_depart}</p>
            <p><strong>Arrivée :</strong> ${conflict.lieu_arrivee}</p>
            <p><strong>Date :</strong> ${conflict.date}</p>

            <h4>Participants :</h4>
            <ul>
              <li>${conflict.utilisateur_note.nom} (${conflict.utilisateur_note.email}) - Note reçue: ${conflict.note_recue} ★</li>
              <li>${conflict.utilisateur_noteur.nom} (${conflict.utilisateur_noteur.email}) - Note donnée: ${conflict.note_donnee} ★</li>
            </ul>

            <h4>Avis :</h4>
            <p>${conflict.commentaire}</p>

            <button class="resolu-btn" data-id="${conflict.trajet_id}">Résolu</button>
            <button class="non-resolu-btn" data-id="${conflict.trajet_id}">Non-résolu</button>
          `;
          container.appendChild(div);
        });

        container.querySelectorAll('.resolu-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            marquerConflit(btn.dataset.id, 'resolu');
          });
        });
        container.querySelectorAll('.non-resolu-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            marquerConflitNonResolu(btn.dataset.id);
          });
        });
      })
      .catch(console.error);
  }

  // Marquer conflit comme résolu
  function marquerConflit(id, etat) {
    fetch('api/modifier_conflit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({trajet_id: id, etat})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadConflits();
    })
    .catch(console.error);
  }

  // Gestion du non-résolu
  function marquerConflitNonResolu(trajet_id) {
    // Ouvrir un prompt pour choisir à qui distribuer les crédits
    const choix = prompt("Distribuer crédits à :\n1 - Les deux\n2 - Participant noté\n3 - Participant noteur\n4 - Aucun");
    if(!choix) return;

    fetch('api/modifier_conflit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({trajet_id, etat: 'non-resolu', distribution_credits: choix})
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.message);
      loadConflits();
    })
    .catch(console.error);
  }
});
