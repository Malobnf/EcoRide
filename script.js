var sidenav = document.querySelector(".deroulant");


function toggleMenu() {
  const menu = document.getElementById('sideMenu');
  menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}

// Ferme le menu si on clique ailleurs
document.addEventListener('click', function (e) {
  const menu = document.getElementById('sideMenu');
  const button = document.querySelector('.deroulant');
  if (!menu.contains(e.target) && !button.contains(e.target)) {
    menu.style.display = 'none';
  }
});


// Statistique incrémentale //

var runCounter1 = function(m, n) {
  var n = n || 0;

  if (n < m) {
    document.getElementById("item1").innerHTML = n;
    window.setTimeout(function() {
      runCounter1(m, ++n);
    }, 10);
  }
};

window.addEventListener("scroll", function scrollHandler1() {
  if (window.scrollY + window.screen.height > 1000) {
    runCounter1(1000);
    window.removeEventListener("scroll", scrollHandler1);
  }
});

// Slider //

document.addEventListener('DOMContentLoaded', () => {
  const images = document.querySelectorAll('.carousel-image');
  const description = document.getElementById('carouselDescription');
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
    description.textContent = activeImg.getAttribute('data-description');
  }
  
  images.forEach((img, index) => {
    img.addEventListener('click', () => {
      updateCarousel(index);
    });
  });

  updateCarousel(currentIndex);
})

// Bouton recherche

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('departVille');
  const button = document.getElementById('searchBtn');

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
  })
})


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
}


// Connexion / Inscription --- Egalement possible de gérer cette interaction en PHP

document.addEventListener('DOMContentLoaded', () => {
  const profileIcon = document.getElementById('icone-profil');

  profileIcon.addEventListener('click', (e) => {
    e.preventDefault(); // Empêche le lien d'ouvrir 'profil.html' par défaut
    const loggedIn = localStorage.getItem('userLoggedIn') === 'true';

    if (loggedIn) {
      window.location.href = 'profil.html';
    }
    else {
      window.location.href = 'connexion.html';
    }
  })
})

    // Fait disparaître / apparaître le formulaire de connexion / inscription lors d'un click

document.getElementById('sign-in-form').addEventListener('click', (e) => {
  e.preventDefault();
  document.getElementById('log-in').style.display = 'none';
  document.getElementById('sign-in').style.display = 'block';
});

// Traitement des filtres

document.getElementById('filtre').addEventListener('submit', function (e) {
  e.preventDefault();

  const type = document.getElementById('voiture').value;
  const prix = document.getElementById('prix').value;
  const duree = document.getElementById('duree').value;
  const note = document.getElementById('note').value;

  console.log("Filtres appliqués : ", {type, prix, duree, note});
})

// Covoiturages disponibles

document.addEventListener("DOMContentLoaded", function () {
  const searchBtn = document.getElementById("searchBtn");
  const departVille = document.getElementById("departVille");
  const departDate = document.getElementById("departDate");
  const resultats = document.getElementById("resultatsCovoit");

  searchBtn.addEventListener('click', function() {
    const ville = departVille.value.trim();
    const date = departDate.value.trim();

    if (ville !== "" && date !== "") {
      resultats.style.display = "block";
    }
    else {
      alert("Veuillez renseigner la ville et la date de départ.");
      resultats.style.display = "none";
    }
  });
});
