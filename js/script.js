document.addEventListener('DOMContentLoaded', () => {
  setDefaultDate();
  initMenuToggle();
  initScrollStats();
  initSlider();
  initRecherche();
  initSwitchFormConnexion();
  initFiltres();
  initResultatsCovoit();
  initRedirectionProfil();
  initReservation();
  initReserverCovoit();
  initLogin();
  initLogout();
  initCredits();
  initModInfo();
  initDepartTime();
  initProposerTrajet();
})

function setDefaultDate() {
  const dateInput = document.getElementById('departDate');
  if (!dateInput) return;

  const today = new Date().toISOString().slice(0, 10);
  dateInput.value = today;
}

function initMenuToggle() {
  const menu = document.getElementById('sideMenu');
  const sidenav = document.querySelector(".deroulant");

  if (!sidenav || !menu) return;

  sidenav.addEventListener('click', () => {
    menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
  });


// Ferme le menu si on clique ailleurs
  document.addEventListener('click', function (e) {
    if (!menu.contains(e.target) && !sidenav.contains(e.target)) {
      menu.style.display = 'none';
    }
  });
}

// Statistique incrémentale //

function initScrollStats() {
  const runCounter1 = function (m, n = 0) {
    if (n < m) {
      document.getElementById("item1").innerHTML = n;
      window.setTimeout(() => runCounter1(m, ++n), 10);
    }
  };

  const item = document.getElementById("item1");
  if (!item) return;

  window.addEventListener("scroll", function scrollHandler1() {
    if (this.window.scrollY + this.window.screen.height > 1000) {
      runCounter1(1000);
      this.window.removeEventListener("scroll", scrollHandler1);
    }
  });
}

// Slider //

function initSlider() {
  const images = document.querySelectorAll('.carousel-image');
  const description = document.getElementById('carouselDescription');
  if (!images.length || !description) return;

  let currentIndex = 2;
  
  function updateCarousel(centerIndex) {
    images.forEach((img, i) => {
      const offset = i - centerIndex;

      if(Math.abs(offset) > 2) {    // Limite l'affichage aux deux images avant et après //
        img.style.display = "none";
      } else {
        img.style.display = "block";
        img.setAttribute("data-position", offset);
      }

      img.classList.toggle("active", offset === 0);
    });

    const activeImg = images[centerIndex];
    if (activeImg) {
      description.textContent = activeImg.getAttribute('data-description');
  }
}
  
  images.forEach((img, index) => {
    img.addEventListener('click', () => updateCarousel(index));
    });

  updateCarousel(currentIndex);
};

// Bouton recherche

function initRecherche() {
  const input = document.getElementById('departVille');
  const button = document.getElementById('searchBtn');

  if(!input || !button) return;

  function lancerRecherche() {
    const destination = input.value.trim();
    // Redirection vers covoit.html
    if (destination) {
      window.location.href = `covoit.html?destination=${encodeURIComponent(destination)}`;      // Uniform Ressource Identifier, transforme les caractères spéciaux ou espaces en code "URL"
    }
  }

  button.addEventListener('click', lancerRecherche); //Bouton 
  input.addEventListener('keydown', (e) => {  // Touche entrée
    if (e.key === 'Enter') {
      lancerRecherche();
    }
  });
};

// Bouton détails/réservation du trajet

function initReservation() {
  const button = document.getElementById('resBtn');

  if (!button) return;

  button.addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = 'reservation.html';
  })
}

// Redirection si connexion


if (window.location.pathname.includes('covoit.html')) {
  const params = new URLSearchParams(window.location.search);
  const destination = params.get('destination');
  const depart = params.get('depart');

  const titre = document.getElementById('input-recherche');
  const resultats = document.getElementById('resultatsCovoit');

  if (destination && titre) {
    titre.textContent = `Trajet vers ${destination}`;
  }
  if (depart && titre) {
    titre.textContent = `Départ de ${depart}`;
  }
  if (resultats) {
    resultats.style.display = 'grid'
  }
};


// Connexion / Inscription 

function initRedirectionProfil() {
  const profileIcon = document.getElementById('icone-profil');
  if (!profileIcon) return;


  profileIcon.addEventListener('click', (e) => {
    e.preventDefault(); // Empêche le lien d'ouvrir 'profil.html' par défaut
    const loggedIn = localStorage.getItem('userLoggedIn') === 'true';

    if (loggedIn) {
      window.location.href = 'profil.html';
    }
    else {
      window.location.href = 'connexion.html';
    }
  });
}

function initSwitchFormConnexion() {
  const signInBtn = document.getElementById('sign-in-form');
  const logInForm = document.getElementById('log-in');
  const signInForm = document.getElementById('sign-in');
  const backToLogin = document.getElementById('back-to-login');

  if (!signInBtn || !logInForm || !signInForm) return;

  signInBtn.addEventListener('click', (e) => {
    e.preventDefault();
    logInForm.style.display = 'none';
    signInForm.style.display = 'block';
  });

  backToLogin.addEventListener('click', (e) => {
    e.preventDefault();
    signInForm.style.display = 'none';
    logInForm.style.display = 'block';
  });
}

function initLogin() {
  const loginBtn = document.getElementById('login-btn');
  if (!loginBtn) return;

  loginBtn.addEventListener('click', async () => {
    const username = document.getElementById('user-name').value.trim();
    const password = document.getElementById('password').value;

    if (!username || !password) {
      alert("Veuillez remplir tous les champs.");
      return;
    }

    try {
      const response = await fetch('php/connexion.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ username, password })
      });

      const result = await response.json();

      if (result.success) {
        localStorage.setItem('userLoggedIn', 'true');
        window.location.href = 'profil.html'; 
      } else {
        alert(result.message || "Identifiants incorrects.")
      }
    } catch (err) {
      console.error("Erreur de connexion : ", err);
      alert("Erreur serveur.");
    }
  });
}

// Déconnexion

function initLogout() {
  const logoutBtn = document.getElementById('logoutBtn');
  if (!logoutBtn) return;

  logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('userLoggedIn');
    localStorage.removeItem('userCredits')
    
    window.location.href = 'deconnexion.php';
  })
}

// Traitement des filtres

function initFiltres() {
  const filtreForm = document.getElementById('filtre');
  if (!filtreForm) return;

  filtreForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const type = document.getElementById('voiture').value;
    const prix = document.getElementById('prix').value;
    const duree = document.getElementById('duree').value;
    const note = document.getElementById('note').value;

    console.log("Filtres appliqués : ", { type, prix, duree, note });
  });
}

// Covoiturages disponibles

function initResultatsCovoit() {
  if (!window.location.pathname.includes('covoit.html')) return;
  
  const params = new URLSearchParams(window.location.search);
  const destination = params.get('destination');
  const depart = params.get('depart');
  const titre = document.getElementById('input-recherche');
  const resultats = document.getElementById("resultatsCovoit");

  if (titre) {
    if (destination) titre.textContent = `Trajet vers ${destination}`;
    if (depart) titre.textContent = `Départ de ${depart}`;
  }

  if (resultats) {
    resultats.style.display = 'grid';
  }
}


// Détails du Trajet

// Reserver

function initReserverCovoit() {
  const reserverBtn = document.getElementById('reserverBtn');
  const placesText = document.getElementById('placesDispo');

  if (!reserverBtn || !placesText) return;

  const idTrajet = parseInt(reserverBtn.getAttribute('data-id'), 10);
  const prixCovoit = 4; // Adapter dynamiquement

  reserverBtn.addEventListener('click', async () => {
    try {
      const response = await fetch('php/reserver.php', {
        method: 'POST',
        headers: {
          'Content-Type' : 'application/json'
        },
        credentials: 'include', // Envoyer le code PHPSESSID ou autre token 
        body: JSON.stringify({ trajetId: idTrajet })
      });

      const data = await response.json();

      if (!data || !data.success) {
        alert(data.message || 'Erreur lors de la réservation');
        if (data.redirect) {
          window.location.href = data.redirect;
        }
        return;
      }

      alert("Réservation confirmée ! Vous allez recevoir un mail de confirmation.");
      if (data.remaining_places !== undefined) {
        placesText.innerHTML = `<strong>Places disponibles :</strong> ${data.remaining_places}`;
      }

    } catch (error) {
      console.error("Erreur de communication avec le serveur", error);
      alert("Erreur technique, Veuillez réessayer.");
    }
  });
};

// Affichage dynamique des crédits de l'utilisateur (connecté)

function initCredits() {
  const creditSpan = document.getElementById('userCredits');
  if (!creditSpan) return;

  fetch('credits.php', {
    credentials: 'include'
  })

    .then(response => response.json())
    .then(data => {
      if (data.success) {
        creditSpan.textContent = data.credits;
      } else {
        creditSpan.textContent = 'N/A';
        console.error(data.message);
      }
    })

    .catch(error => {
      creditSpan.textContent = 'Erreur';
      console.error("Erreur de récupération des crédits :", error)
    });
}

// CREER UN TRAJET

// Set heure de départ de base

function initDepartTime() {
  const timeControl = document.getElementById('departHeure');
  if (!timeControl) return;

  for (let h = 0; h < 24; h++) {
    for (let m = 0; m < 60; m += 15) {
      const hh = h.toString().padStart(2, '0');
      const mm = m.toString().padStart(2, '0');
      const option = document.createElement('option');
      option.value = `${hh}:${mm}`;
      option.textContent = `${hh}:${mm}`;
      timeControl.appendChild(option);
    }
  }

  // Heure arrondie au quart d'heure supérieur
  const now = new Date();
  let minutes = now.getMinutes();
  let hours = now.getHours();

  minutes = Math.ceil(minutes/15) * 15;

  if (minutes === 60) {
    minutes = 0;
    hours = (hours + 1) % 24;
  }
  
  const formatted = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
  timeControl.value = formatted;
}

// Confirmer la création du trajet
function initProposerTrajet() {
  const btn = document.getElementById('confBtn');
  if (!btn) return;

  btn.addEventListener('click', () => {
    const depart = document.getElementById('departVille').value.trim();
    const arrivee = document.getElementById('arriveeVille').value.trim();
    const date = document.getElementById('departDate').value.trim();
    const heure = document.getElementById('departHeure').value.trim();
    const prix = document.getElementById('setPrixTrajet').value.trim();
    const passagers = document.getElementById('setNbPassagers').value.trim();
    const voitureRadio = document.querySelector('input[name="voiture"]:checked');

    if (!depart || !arrivee || !date || !heure || !prix || !passagers || !voitureRadio) {
      alert("Veuillez remplir tous les champs du trajet");
      return;
    }

    const voiture = voitureRadio.value;

    const formData = new FormData();
    formData.append('depart', depart);
    formData.append('arrivee', arrivee);
    formData.append('date', date);
    formData.append('heure', heure);
    formData.append('prix', prix);
    formData.append('passagers', passagers);
    formData.append('voiture', voiture);

    fetch('creer-trajet.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const resumeText = `
        <strong>Départ : </strong>${depart}<br>
        <strong>Arrivée : </strong>${arrivee}<br>
        <strong>Date : </strong>${date}<br>
        <strong>Heure : </strong>${heure}<br>
        <strong>Prix : </strong>${prix}<br>
        <strong>Places disponibles : </strong>${passagers}<br>
        <strong>Véhicule : </strong>${voiture}<br>`;

        document.getElementById('resume-content').innerHTML = resumeText;
        document.getElementById('confTrajet'.classList.remove('hidden'));

        // Fermer le popup
        document.getElementById('closePopupBtn').addEventListener('click', () => {
          document.getElementById('confTrajet').classList.add('hidden');
        });
      } else {
        alert('Erreur : ' + data.message);
      }
    })

// Modification des informations personnelles

function initModInfo() {
  const profilBtn = document.getElementById('profilBtn');
  const modInfo = document.getElementById('modInfo');

  if (!profilBtn || !modInfo) return;

  profilBtn.addEventListener('click', () => {
    modInfo.style.display = 'block'
  })
}