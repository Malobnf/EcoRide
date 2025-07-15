document.addEventListener('DOMContentLoaded', () => {
  initMenuToggle()
  initScrollStats();
  initSlider();
  initRecherche();
  initSwitchFormConnexion();
  initFiltres();
  initResultatsCovoit();
  initRedirectionProfil();
})


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

  if (!signInBtn || !logInForm || !signInForm) return;

  signInBtn.addEventListener('click', (e) => {
    e.preventDefault();
    logInForm.style.display = 'none';
    signInForm.style.display = 'block';        // Fait disparaître / apparaître le formulaire de connexion / inscription lors d'un click
  });
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


// Détails du trajet / popup 