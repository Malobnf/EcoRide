var sidenav = document.getElementById("sideNav");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");

openBtn.onclick = openNav;
closeBtn.onclick = closeNav;

function openNav() {
  sidenav.classList.add("active");
}

function closeNav() {
  sidenav.classList.remove("active");
}

function toggleMenu() {
  const menu = document.getElementById('sideMenu');
  menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}

// Ferme le menu si on clique ailleurs
document.addEventListener('click', function (e) {
  const menu = document.getElementById('sideMenu');
  const button = document.querySelector('.menu-button');
  if (!menu.contains(e.target) && !button.contains(e.target)) {
    menu.style.display = 'none';
  }
});