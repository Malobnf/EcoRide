// Menu déroulant

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


// Statistique incrémentale

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

// Slider

document.addEventListener('DOMContentLoaded', () => {
  const images = document.querySelectorAll('.carousel-image');
  const description = document.getElementById('carouselDescription');
  let currentIndex = 2;
  
  function updateCarousel(centerIndex) {
    images.forEach((img, i) => {
      const offset = i - centerIndex;

      if(Math.abs(offset) > 2) {    // Limite l'affichage aux deux images avant et après
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
});

// Bouton recherche

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('destination');
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
  });
});


if (window.location.pathname.includes('covoit.html')) {
  const params = new URLSearchParams(window.location.search);
  const destination = params.get('destination');

  if (destination) {
    const titre = document.getElementById('input-destination');
    if (titre) {
      titre.textContent = `Trajet vers ${destination}`;
    }
  }
}


// Recherche de trajet //

document.addEventListener('click', () => {
  const trajet = document.getElementById('destination');

})