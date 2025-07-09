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

  images.forEach((img) => {
    img.addEventListener('click', () => {
      images.forEach((i) => i.classList.remove('active'));  // on click, retire la classe active de toutes les images //

      img.classList.add('active'); // on click, ajoute la classe active à l'image cliquée //
    });
  });
});